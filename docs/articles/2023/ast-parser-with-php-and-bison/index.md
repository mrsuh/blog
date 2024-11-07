# AST parser with PHP and Bison

> Read this [post](/articles/2023/php-skeleton-for-bison/) if you don't know what Bison is.

I already have the [Bison AST parser](https://github.com/mrsuh/php-ast), but this time I will do it without PHP FFI.

First, we need to install the [PHP skeleton package](https://github.com/mrsuh/php-bison-skeleton) to build the parser and [tree printer package](https://github.com/mrsuh/tree-printer) to print `AST`.
```bash
composer require --dev mrsuh/php-bison-skeleton
composer require mrsuh/tree-printer
```

It will more readable if we separate code from `printer.php` to individual files.
```bash
.
├── /ast-parser
    ├── /bin
    │   └── parse.php # entry point for parser
    ├── /lib
    │   └── parser.php # generated file
    ├── /src
    │   ├── Lexer.php
    │   └── Node.php # AST node
    └── grammar.y       
```

To print `AST` with the tree printer package `Node` class must implement `Mrsuh\Tree\NodeInterface`.

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
        $line = $this->name;
        if (!empty($this->value)) {
            $line .= sprintf(" '%s'", $this->value);
        }

        return $line;
    }
}
```

`Lexer` is not modified from [previous post](/articles/2023/php-skeleton-for-bison/), but this time we will put it in a separate file `src/Lexer.php`.

`src/Lexer.php`
```php
<?php

namespace App;

class Lexer implements LexerInterface {

    private array $words;
    private int   $index = 0;
    private int   $value = 0;

    public function __construct($resource)
    {
        $this->words = explode(' ', trim(fgets($resource)));
    }

    public function yyerror(string $message): void
    {
        printf("%s\n", $message);
    }

    public function getLVal()
    {
        return $this->value;
    }

    public function yylex(): int
    {
        if ($this->index >= count($this->words)) {
            return LexerInterface::YYEOF;
        }

        $word = $this->words[$this->index++];
        if (is_numeric($word)) {
            $this->value = (int)$word;

            return LexerInterface::T_NUMBER;
        }

        return ord($word);
    }
}
```

For example, `Lexer` will translate the expression `10 + 20 - 30` into this:

| word | token                          | value |
|------|--------------------------------|-------|
| 10   | LexerInterface::T_NUMBER (258) | 10    |
| +    | ASCII (43)                     |       |
| 20   | LexerInterface::T_NUMBER (258) | 20    |
| -    | ASCII (45)                     |       |
| 30   | LexerInterface::T_NUMBER (258) | 30    |
|      | LexerInterface::YYEOF (0)      |       |

It's time to create the `grammar.y` file and build `lib/parser.php`
You can define `%code` blocks, so Bison will render code as is in `printer.php`

`grammar.y`
```php
%code imports { // code imports };
%code parser  { // code parser }; 
%code init    { // code init };           
```

`printer.php`
```php
<?php

// code imports

class Parser {
    // code parser
    public function __construct() {
        // code init
    }
}
```

We will use block `%code parser` to define variables and methods to store `AST` into the `Parser` class.
Bison has reserved the symbol `$` in grammar actions.
It's very sad for PHP developers, but we can call the function `setAst()` with `self::setAst()` instead of `$this->setAst()`.

`grammar.y`
```php
%define api.parser.class {Parser}
%define api.namespace {App}
%code parser {
    private Node $ast;
    public function setAst(Node $ast): void { $this->ast = $ast; }
    public function getAst(): Node { return $this->ast; }
}

%token T_NUMBER

%left '-' '+'

%%
start:
  expression                 {  self::setAst($1); }
;

expression:
  T_NUMBER                   { $$ = new Node('NUMBER', $1); }
| expression '+' expression  { $$ = new Node('OPERATION_PLUS', '', [$1, $3]);  }
| expression '-' expression  { $$ = new Node('OPERATION_MINUS', '', [$1, $3]);  }
;
```

```bash
bison -S vendor/mrsuh/php-bison-skeleton/src/php-skel.m4 -o lib/parser.php grammar.y
```
Command options:
* `-S vendor/mrsuh/php-bison-skeleton/src/php-skel.m4` - path to `skeleton` file
* `-o parser.php` - output parser file
* `grammar.y` - our grammar file

And final PHP file is the entry point `bin/parse.php`.

`bin/parse.php`
```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Lexer;
use App\Parser;
use Mrsuh\Tree\Printer;

$lexer  = new Lexer(STDIN);
$parser = new Parser($lexer);
if (!$parser->parse()) {
    exit(1);
}

$printer = new Printer(STDOUT);
$printer->print($parser->getAst());
```

We need to add a special autoload section to `composer.json` for generated `lib/parser.php` file.

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

Ok. Our parser is ready and we can test it:
```bash
php bin/parse.php <<< "1 + 2 - 3"
.
├── OPERATION_MINUS
    ├── OPERATION_PLUS
    │   ├── NUMBER '1'
    │   └── NUMBER '2'
    └── NUMBER '3'
```

Try to parse big expression:
```bash
php bin/parse.php <<< "1 + 2 - 3 + 4 - 5 + 6 - 7 + 8 - 9 + 10"
.
├── OPERATION_PLUS
    ├── OPERATION_MINUS
    │   ├── OPERATION_PLUS
    │   │   ├── OPERATION_MINUS
    │   │   │   ├── OPERATION_PLUS
    │   │   │   │   ├── OPERATION_MINUS
    │   │   │   │   │   ├── OPERATION_PLUS
    │   │   │   │   │   │   ├── OPERATION_MINUS
    │   │   │   │   │   │   │   ├── OPERATION_PLUS
    │   │   │   │   │   │   │   │   ├── NUMBER '1'
    │   │   │   │   │   │   │   │   └── NUMBER '2'
    │   │   │   │   │   │   │   └── NUMBER '3'
    │   │   │   │   │   │   └── NUMBER '4'
    │   │   │   │   │   └── NUMBER '5'
    │   │   │   │   └── NUMBER '6'
    │   │   │   └── NUMBER '7'
    │   │   └── NUMBER '8'
    │   └── NUMBER '9'
    └── NUMBER '10'
```
Great!

You can get the parser source code [here](https://github.com/mrsuh/php-bison-skeleton/tree/master/examples/calc-ast) and test it by yourself.

Some useful links:
* [PHP skeleton library](https://github.com/mrsuh/php-bison-skeleton/tree/master/examples)
* more [examples](https://github.com/mrsuh/php-bison-skeleton/tree/master/examples)
* [Bison docker image](https://github.com/mrsuh/docker-bison)
* [Parsing with PHP, Bison, and re2c](/articles/2022/parsing-with-php-bison-and-re2c/)
