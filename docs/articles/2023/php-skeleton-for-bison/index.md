# PHP Skeleton for Bison

## What is Bison?

[Bison](https://www.gnu.org/software/bison) is a parser generator.
For example, it can help you to build a parser to parse your code into [AST](https://en.wikipedia.org/wiki/Abstract_syntax_tree):
```php
<?php

namespace App;

class Test
{
    public function test($foo) {}
}
```

```bash
.
├── ZEND_AST_STMT_LIST
    ├── ZEND_AST_NAMESPACE
    │   └── ZEND_AST_ZVAL 'App'
    └── ZEND_AST_CLASS 'Test'
        └── ZEND_AST_STMT_LIST
            └── ZEND_AST_METHOD 'test'
                └── ZEND_AST_PARAM_LIST
                    └── ZEND_AST_PARAM
                        └── ZEND_AST_ZVAL 'foo'
```

There is software that uses Bison:

| software   | grammar.y                                                                      |
|------------|--------------------------------------------------------------------------------|
| PHP        | https://github.com/php/php-src/blob/master/Zend/zend_language_parser.y         |
| Bash       | https://git.savannah.gnu.org/cgit/bash.git/tree/parse.y                        |
| Ruby       | https://github.com/ruby/ruby/blob/master/parse.y                               |
| MySQL      | https://github.com/mysql/mysql-server/blob/8.0/sql/sql_yacc.yy                 |
| PostgreSQL | https://github.com/postgres/postgres/blob/master/src/backend/parser/gram.y     |
| CMake      | https://github.com/Kitware/CMake/blob/master/Source/LexerParser/cmExprParser.y |

## How Bison works?

![Bison](./images/diagram.svg)

Bison takes your `grammar.y` file, parses it, extracts all definitions, and then constructs a bunch of tables like this:
```php
$yytable = [
    6, 3, 7, 20, 8, 51, 28, 1, 52, 4,
    9, 13, 10, 29, 15, 30, 18, 31, 16, 19,
    32, 22, 33, 34, 23, 24, 35, 11, 37, 25,
    21, 38, 39, 26, 45, 0, 40, 42, 0, 43,
    41, 0, 0, 49, 0, 0, 0, 0, 0, 47,
    48, 0, 50, 0, 53, 54
];
```

Then, this data is passed to a template that is called `skeleton`.
This `skeleton` is a special file written in [M4](https://en.wikipedia.org/wiki/M4_(computer_language)) language that renders your `parser.php` file.
By default, Bison supports C/C++/D/Java languages, but you can extend it with your own `skeleton` file.

## Simple calculator

Let's make a simple calculator with Bison and PHP. It will parse expression from `stdin` and print result.

First, we must install [PHP skeleton package](https://github.com/mrsuh/php-bison-skeleton).
```bash
composer require --dev mrsuh/php-bison-skeleton
```

Then we define a simple grammar file.

`grammar.y`
```php
%define api.parser.class {Parser}

%token T_NUMBER

%left '-' '+'

%%
start:
  exp                { printf("%f\n", $1); }
;

exp:
  T_NUMBER           { $$ = $1; }
| exp '+' exp        { $$ = $1 + $3;  }
| exp '-' exp        { $$ = $1 - $3;  }
;

%%
```

Let's build a parser from `grammar.y`:
```bash
bison -S vendor/mrsuh/php-bison-skeleton/src/php-skel.m4 -o parser.php grammar.y
```
Command options:
* `-S vendor/mrsuh/php-bison-skeleton/src/php-skel.m4` - path to `skeleton` file
* `-o parser.php` - output parser file
* `grammar.y` - our grammar file

For now, `parser.php` does nothing. But we can see the interface `LexerInterface` inside it:

`parser.php`
```php
interface LexerInterface
{
    public const YYEOF = 0;    
    public const YYerror = 256;    
    public const YYUNDEF = 257;
    public const T_NUMBER = 258; /** %token T_NUMBER */
    ...
}
...
```

Interface contains constants with our tokens from `grammar.y` file and some special values for the end of file or errors.
With this interface, we can write our class `Lexer` to parse calculation expressions into tokens.

```php
class Lexer implements LexerInterface {

    private array $words;
    private int   $index = 0;
    private int   $value = 0;

    public function __construct($resource)
    {
        $this->words = explode(' ', trim(fgets($resource)));
    }

    public function getLVal()
    {
        return $this->value;
    }

    public function yylex(): int
    {
         $this->value = 0;
         
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

Every time we call the function `Lexer::yylex()`, it will return the token's identifier and store the word into the `value` property.
For example, the expression `10 + 20 - 30` will translate into this:

| word | token                          | value |
|------|--------------------------------|-------|
| 10   | LexerInterface::T_NUMBER (258) | 10    |
| +    | ASCII (43)                     |       |
| 20   | LexerInterface::T_NUMBER (258) | 20    |
| -    | ASCII (45)                     |       |
| 30   | LexerInterface::T_NUMBER (258) | 30    |
|      | LexerInterface::YYEOF (0)      |       |

And the last part.
We must instantiate lexer, parser and bind them.
```php
$lexer  = new Lexer(STDIN);
$parser = new Parser($lexer);
if (!$parser->parse()) {
    exit(1);
}
```

Let's assemble all parts above into a single `grammar.y` file.

`grammar.y`
```php
%define api.parser.class {Parser}

%token T_NUMBER

%left '-' '+'

%%
start:
  exp                { printf("%f\n", $1); }
;

exp:
  T_NUMBER           { $$ = $1; }
| exp '+' exp        { $$ = $1 + $3;  }
| exp '-' exp        { $$ = $1 - $3;  }
;

%%

class Lexer implements LexerInterface {

    private array $words;
    private int   $index = 0;
    private int   $value = 0;

    public function __construct($resource)
    {
        $this->words = explode(' ', trim(fgets($resource)));
    }

    public function getLVal()
    {
        return $this->value;
    }

    public function yylex(): int
    {
         $this->value = 0;
         
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

$lexer  = new Lexer(STDIN);
$parser = new Parser($lexer);
if (!$parser->parse()) {
    exit(1);
}
```

Build it:
```bash
bison -S vendor/mrsuh/php-bison-skeleton/src/php-skel.m4 -o parser.php grammar.y
```

It's time to test our parser.
```bash
php parser.php <<< "1 + 2 - 3 + 4 - 5 + 6 - 7 + 8 - 9 + 10"
7
```

It works!

Some useful links:
* [PHP skeleton library](https://github.com/mrsuh/php-bison-skeleton/tree/master/examples)
* `parser.php` [source code](https://github.com/mrsuh/php-bison-skeleton/tree/master/examples/calc-pull)
* more [examples](https://github.com/mrsuh/php-bison-skeleton/tree/master/examples)
* [Bison docker image](https://github.com/mrsuh/docker-bison)
* [Parsing with PHP, Bison, and re2c](/articles/2022/parsing-with-php-bison-and-re2c/)
