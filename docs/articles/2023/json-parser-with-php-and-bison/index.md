# JSON parser with PHP and Bison

> Read this [post](/articles/2023/php-skeleton-for-bison/) if you don't know what Bison is.

Today I'll try to parse JSON into AST and compare it with the native PHP function `json_decode()`.
To test our parser I will use this JSON file:

`test.json`
```json
{
    "fieldString": "string",
    "fieldNumber": 99,
    "fieldBoolTrue": true,
    "fieldBoolFalse": false,
    "fieldNull": null,
    "fieldEmptyArray": [],
    "fieldEmptyObject": {},
    "fieldArray": [
        "string",
        99,
        true,
        false,
        null,
        {},
        []
    ]
}
```

First, we need to install PHP dependencies.
```bash
composer require --dev mrsuh/php-bison-skeleton
composer require mrsuh/tree-printer
composer require doctrine/lexer
```

* [mrsuh/php-bison-skeleton](https://github.com/mrsuh/php-bison-skeleton) - to build PHP parser with Bison
* [mrsuh/tree-printer](https://www.doctrine-project.org/projects/doctrine-lexer/en/1.2/index.html) - to print `AST`
* [doctrine/lexer](https://www.doctrine-project.org/projects/doctrine-lexer/en/1.2/index.html) - to parse text into tokens

We will store our files like this:
```bash
.
├── /ast-parser
    ├── /bin
    │   └── parse.php # entry point to parse JSON
    ├── /lib
    │   └── parser.php # generated file
    ├── /src
    │   ├── Lexer.php
    │   └── Node.php # AST node
    └── grammar.y       
```

The `Node` class must implement `Mrsuh\Tree\NodeInterface` to print `AST`.

`src/Node.php`
```php
<?php

namespace App;

use Mrsuh\Tree\NodeInterface;

class Node implements NodeInterface
{
    private string $name;
    private string $value;
    /** @var Node[] */
    private array $children;

    public function __construct(string $name, string $value, array $children = [])
    {
        $this->name     = $name;
        $this->value    = $value;
        $this->children = $children;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function __toString(): string
    {
        if (!empty($this->value)) {
            return sprintf("%s: '%s'", $this->name, $this->value);
        }

        return $this->name;
    }
}
```

I'll use the Doctrine lexer library. It helps to parse complex text.

`src/Lexer.php`
```php
<?php

namespace App;

use Doctrine\Common\Lexer\AbstractLexer;

class Lexer extends AbstractLexer implements LexerInterface
{
...
    protected function getCatchablePatterns(): array
    {
        return [
            '\:',
            '\{',
            '\}',
            '\[',
            '\]',
            '\,',
            "\"[^\"]+\"",
            'true',
            'false',
            'null',
        ];
    }

    protected function getNonCatchablePatterns(): array
    {
        return [
            ' ',
            '\n'
        ];
    }

    protected function getType(&$value): int
    {
        if (in_array($value, [':', '{', '}', '[', ']', ','], true)) {
            return ord($value);
        }

        if (is_numeric($value)) {
            return LexerInterface::T_NUMBER;
        }

        switch (strtolower($value)) {
            case 'true':
            case 'false':
                return LexerInterface::T_BOOL;
            case 'null':
                return LexerInterface::T_NULL;
        }

        return LexerInterface::T_STRING;
    }
...
}
```

For example, `Lexer` will translate the JSON
```json
{
    "array": [
        "string",
        99,
        true,
        false,
        null
    ]
}
```

into this:

| word     | token                          |
|----------|--------------------------------|
| {        | ASCII (123)                    |
| "array"  | LexerInterface::T_STRING (258) |
| :        | ASCII (58)                     |
| [        | ASCII (91)                     |
| "string" | LexerInterface::T_STRING (258) |
| ,        | ASCII (44)                     |
| 99       | LexerInterface::T_NUMBER (259) |
| ,        | ASCII (44)                     |
| true     | LexerInterface::T_BOOL (260)   |
| ,        | ASCII (44)                     |
| false    | LexerInterface::T_BOOL (260)   |
| ,        | ASCII (44)                     |
| null     | LexerInterface::T_NULL (261)   |
| ,        | ASCII (44)                     |
| ]        | ASCII (93)                     |
| }        | ASCII (125)                    |
|          | LexerInterface::YYEOF (0)      |

Time to create `grammar.y` file and build `lib/parser.php`

PHP already has the native function `json_decode()` and it uses Bison to generate a C parser.
I think we can get ready [Bison grammar file](https://github.com/php/php-src/blob/master/ext/json/json_parser.y) from the php-src repository and modify it.
The grammar file is very small because [JSON standard](https://www.json.org/json-en.html) is very simple.

We will use block `%code parser` to define variables and methods to store `AST` into the `Parser` class.

`grammar.y`
```php
%define api.parser.class {Parser}
%define api.namespace {App}
%code parser {
    private Node $ast;
    public function setAst(Node $ast): void { $this->ast = $ast; }
    public function getAst(): Node { return $this->ast; }
}

%token T_STRING
%token T_NUMBER
%token T_BOOL
%token T_NULL

%%
start:
  value  { self::setAst($1); }
;

object:
'{' members '}' { $$ = $2; }
;

members:
  %empty             { $$ = []; }
| member             { $$ = [$1]; }
| members ',' member { $$ = $1; $$[] = $3; }
;

member:
  T_STRING ':' value  { $$ = new Node('T_STRING', $1, [$3]); }
;

array:
'['  elements ']' { $$ = $2; }
;

elements:
  %empty             { $$ = []; }
| value              { $$ = [$1]; }
| elements ',' value { $$ = $1; $$[] = $3; }
;

value:
  object   { $$ = new Node('T_OBJECT', '', $1); }
| array    { $$ = new Node('T_ARRAY', '', $1); }
| T_STRING { $$ = new Node('T_STRING', $1); }
| T_NUMBER { $$ = new Node('T_NUMBER', $1); }
| T_BOOL   { $$ = new Node('T_BOOL', $1); }
| T_NULL   { $$ = new Node('T_NULL', $1); }
;

%%
```

```bash
bison -S vendor/mrsuh/php-bison-skeleton/src/php-skel.m4 -o lib/parser.php grammar.y
```
Command options:
* `-S vendor/mrsuh/php-bison-skeleton/src/php-skel.m4` - path to `skeleton` file
* `-o parser.php` - output parser file
* `grammar.y` - our grammar file

The final PHP file is the entry point for the parser.

`bin/parse.php`
```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Parser;
use App\Lexer;
use Mrsuh\Tree\Printer;

$lexer  = new Lexer(fopen($argv[1], 'r'));
$parser = new Parser($lexer);
if (!$parser->parse()) {
    exit(1);
}

$printer = new Printer();
$printer->print($parser->getAst());
```

Autoload for generated `lib/parser.php` file.

`composer.json`
```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        },
        "files": ["lib/parser.php"]
    },
    ...
}
```

Finally, we can test our parser.
```bash
php bin/parse.php test.json 
.
├── T_OBJECT
    ├── T_STRING: 'fieldString'
    │   └── T_STRING: 'string'
    ├── T_STRING: 'fieldNumber'
    │   └── T_NUMBER: '99'
    ├── T_STRING: 'fieldBoolTrue'
    │   └── T_BOOL: 'true'
    ├── T_STRING: 'fieldBoolFalse'
    │   └── T_BOOL: 'false'
    ├── T_STRING: 'fieldNull'
    │   └── T_NULL: 'null'
    ├── T_STRING: 'fieldEmptyArray'
    │   └── T_ARRAY
    ├── T_STRING: 'fieldEmptyObject'
    │   └── T_OBJECT
    └── T_STRING: 'fieldArray'
        └── T_ARRAY
            ├── T_STRING: 'string'
            ├── T_NUMBER: '99'
            ├── T_BOOL: 'true'
            ├── T_BOOL: 'false'
            ├── T_NULL: 'null'
            ├── T_OBJECT
            └── T_ARRAY
```
It works!

I think it will be cool if we compare the native `json_decode()` function and our parser.
First, I need a JSON file for benchmarks. I can get JSON info about Bulbasaur pokemon from API https://pokeapi.co.
```bash
curl 'https://pokeapi.co/api/v2/pokemon/bulbasaur' > bench.json
```
The file weight is 215KB.

We need to modify our `grammar.y` file to avoid `Node` creating.

`grammar-bench.y`
```php
...
value:
  object   { $$ = $1; }
| array    { $$ = $1; }
| T_STRING { $$ = $1; }
| T_NUMBER { $$ = $1; }
| T_BOOL   { $$ = $1; }
| T_NULL   { $$ = $1; }
...
```

```bash
bison -S ../../src/php-skel.m4 -o lib/parser.php grammar-bench.y 
```

We are ready to start the comparison.

PHP 8.2
```bash
php vendor/bin/phpbench run tests --report=my-report
+-------------+----------+----------+--------+
| subject     | mem_peak | mode     | rstdev |
+-------------+----------+----------+--------+
| benchNative | 2.539mb  | 1.570ms  | ±0.89% |
| benchBison  | 12.443mb | 84.283ms | ±1.08% |
+-------------+----------+----------+--------+
```

PHP 8.1
```bash
php vendor/bin/phpbench run tests --report=my-report
+-------------+----------+----------+--------+
| subject     | mem_peak | mode     | rstdev |
+-------------+----------+----------+--------+
| benchNative | 2.593mb  | 1.595ms  | ±0.68% |
| benchBison  | 18.471mb | 87.471ms | ±0.68% |
+-------------+----------+----------+--------+
```

PHP 8.0
```bash
php vendor/bin/phpbench run tests --report=my-report
+-------------+----------+----------+--------+
| subject     | mem_peak | mode     | rstdev |
+-------------+----------+----------+--------+
| benchNative | 2.700mb  | 1.586ms  | ±0.90% |
| benchBison  | 18.578mb | 87.533ms | ±0.83% |
+-------------+----------+----------+--------+
```

PHP 7.4
```bash
php vendor/bin/phpbench run tests --report=my-report
+-------------+----------+-----------+--------+
| subject     | mem_peak | mode      | rstdev |
+-------------+----------+-----------+--------+
| benchNative | 2.857mb  | 1.725ms   | ±1.00% |
| benchBison  | 18.735mb | 105.099ms | ±0.91% |
+-------------+----------+-----------+--------+
```

PHP Bison parser shows the best result with PHP 8.2.
It is ~56 times slower than the native `json_decode()` function.

I hope it was interesting for you!

You can get the parser source code [here](https://github.com/mrsuh/php-bison-skeleton/tree/master/examples/json-ast) and test it by yourself.

Some useful links:
* [PHP skeleton library](https://github.com/mrsuh/php-bison-skeleton)
* [Bison docker image](https://github.com/mrsuh/docker-bison)
* [PHP Skeleton for Bison](/articles/2023/php-skeleton-for-bison/)
* [AST parser with PHP and Bison](/articles/2023/ast-parser-with-php-and-bison/)
* [Nginx parser with PHP and Bison](/articles/2023/nginx-parser-with-php-and-bison/)
