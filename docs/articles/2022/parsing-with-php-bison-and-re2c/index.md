# Parsing with PHP, Bison and re2c

The `PHP` engine uses `Bison` and `re2c` to parse code into `AST`.
I'll show you how we can use these tools with and without `PHP` to parse any structured text.

[re2c](https://re2c.org) is an open-source lexer generator. It uses regular expressions to recognize tokens.

A simple lexer example:

lexer.l
```c
#include <stdio.h>

const char *lex(const char *s)
{
    /*!re2c
        re2c:yyfill:enable = 0;
        re2c:define:YYCTYPE = char;
        re2c:define:YYCURSOR = s;

        [0-9]+ { return "TOK_NUMBER"; }
        *      { return "TOK_ANY"; }
    */

    return "";
}

int main(int argc, char *argv[])
{
    printf("%s\n", lex(argv[1]));

    return 0;
}
```

Lexer reads `stdin`, determines `token` using a regular expression and prints `token`.

The `re2c` replaces the `/*!re2c ... */` block with actual code:
```bash
re2c lexer.l > lexer.c
```

Lexer code after `re2c`:
lexer.c
```c
#line 1 "lexer.l"
#include <stdio.h>

const char *lex(const char *s)
{
    
#line 9 "<stdout>"
{
	char yych;
	yych = *s;
	switch (yych) {
		case '0':
		case '1':
		case '2':
		case '3':
		case '4':
		case '5':
		case '6':
		case '7':
		case '8':
		case '9': goto yy2;
		default: goto yy1;
	}
yy1:
	++s;
#line 11 "lexer.l"
	{ return "TOK_ANY"; }
#line 30 "<stdout>"
yy2:
	yych = *++s;
	switch (yych) {
		case '0':
		case '1':
		case '2':
		case '3':
		case '4':
		case '5':
		case '6':
		case '7':
		case '8':
		case '9': goto yy2;
		default: goto yy3;
	}
yy3:
#line 10 "lexer.l"
	{ return "TOK_NUMBER"; }
#line 49 "<stdout>"
}
#line 12 "lexer.l"


    return "";
}

int main(int argc, char *argv[])
{
    printf("%s\n", lex(argv[1]));

    return 0;
}
```

Let's compile and test it:
```bash
gcc lexer.c -o lexer
```

```bash
./lexer 123
TOK_NUMBER
```

```bash
./lexer test
TOK_ANY
```

Not bad. Now we can recognize numbers.

The next part is parsing.

[Bison](https://www.gnu.org/software/bison) is an open-source context-free parser generator.
It can generate a parser from [BNF](https://en.wikipedia.org/wiki/Backus–Naur_form).

A simple Bison example:

parser.y
```c
%code top {
  #include <ctype.h>  /* isdigit. */
  #include <stdio.h>  /* printf. */

  int yylex();
  void yyerror(char const *);
}

%define api.header.include {"parser.h"}

%define api.value.type {double}
%token TOK_NUMBER
%type  expr

%left '-' '+'

%% /* The grammar follows. */
input:
  %empty
| expr '\n'  { printf ("%.10g\n", $1); }
;

expr:
  TOK_NUMBER    { $$ = $1; }
| expr '+' expr { $$ = $1 + $3; }
| expr '-' expr { $$ = $1 - $3; }
;

%%

int yylex()
{
    int c;

    /* Ignore white space, get first nonwhite character.  */
    while ((c = getchar()) == ' ' || c == '\t')
    {
        continue;
    }

    if (c == EOF)
    {
        return YYEOF;
    }

    /* Char starts a number => parse the number. */
    if (c == '.' || isdigit(c))
    {
        ungetc(c, stdin);
        if (scanf("%lf", &yylval) != 1)
        {
            return YYUNDEF;
        }

        return TOK_NUMBER;
    }

    return c;
}

void yyerror(char const *s)
{
    fprintf(stderr, "%s\n", s);
}

int main()
{
    return yyparse();
}
```

The main parser function is `yyparse`. `Bison` generates it himself.
Each time the parser needs the next token it calls the `yylex` function.
The `yylex` function reads a word, recognizes the word's `token`, stores the word in `yyval` and returns the `token`.

We have changed the type of `yyval` to store a `double` number.
```c
%define api.value.type {double}
```

Bison grammar says:
* parse text with numbers and signs (for example `1 + 2 - 3`);
* math it;
* print the result.

```c
input:
  %empty
| expr '\n'  { printf ("%.10g\n", $1); }
;

expr:
  TOK_NUMBER    { $$ = $1 }
| expr '+' expr { $$ = $1 + $3; }
| expr '-' expr { $$ = $1 - $3; }
;
```

Generate a parser and compile it:
```bash
bison --header -o parser.c parser.y
gcc parser.c -o parser
```

```bash
./parser
1 + 7
8
```

It works!

Let's combine `Bison` and `re2c` to parse simple math expressions into an [AST](https://en.wikipedia.org/wiki/Abstract_syntax_tree).

First we need an `AST` node structure and functions to create this structure:
[ast.c](https://github.com/mrsuh/calc-parser/blob/main/src/ast.c)
```c
typedef struct parser_ast {
    const char* kind;
    const char* value;
    struct parser_ast* children[2];
} parser_ast;

parser_ast* create_ast(const char* kind, const char* value);

parser_ast* create_ast_operation(const char* kind, parser_ast* left, parser_ast* right);
```

We need a `char*` type for `TOK_NUMBER` and a `parser_ast*` type for AST.
The `yyval` type has become the `parser_t` structure:
[ast.c](https://github.com/mrsuh/calc-parser/blob/main/src/ast.c)
```c
typedef union parser_t {
    char* number;
    parser_ast* ast;
} parser_t;
```

Let's rewrite `parser.y` with a new `yyval` type and `AST` functions:
[parser.y](https://github.com/mrsuh/calc-parser/blob/main/src/parser.y)
```c
%require "3.8"

%code top {
  #include <stdio.h>  /* fprintf. */
  #include "ast.h"

  int yylex(char **content);
  void yyerror(char **content, char const *);
  parser_ast *ast = NULL;
}

%param {char **content}

%define api.header.include {"parser.h"}
%define api.value.type {parser_t}

%token <number> TOK_NUMBER
%type  <ast> expr

%left '-' '+'

%%

line:
  %empty
|  expr { ast = $1; }
;

expr:
    TOK_NUMBER    { $$ = create_ast("NUMBER", $1); }
|   expr '+' expr { $$ = create_ast_operation("OPERATION_PLUS", $1, $3); }
|   expr '-' expr { $$ = create_ast_operation("OPERATION_MINUS", $1, $3); }
;

%%

void yyerror (char **content, char const *s)
{
  fprintf (stderr, "%s\n", s);
}

parser_ast* parse(char *content) {

    yyparse(&content);

    return ast;
}

int main(int argc, char *argv[])
{
    ast = parse(argv[1]);
    if (ast == NULL) {
       return 1;
    }

    dump_ast(ast, 0);

    return 0;
}
```

The new grammar creates a `parser_ast` structure with `AST` functions:
[parser.y](https://github.com/mrsuh/calc-parser/blob/main/src/parser.y)
```c
expr:
    TOK_NUMBER    { $$ = create_ast("NUMBER", $1); }
|   expr '+' expr { $$ = create_ast_operation("OPERATION_PLUS", $1, $3); }
|   expr '-' expr { $$ = create_ast_operation("OPERATION_MINUS", $1, $3); }
```

Let's rewrite the `yylex` function with `re2c` and a new `yyval` type:
[lexer.l](https://github.com/mrsuh/calc-parser/blob/main/src/lexer.l)
```c
#include "ast.h"
#include "parser.h"
#include <stdio.h> // sprintf
#include <stdlib.h> // malloc

int yylex(char **content)
{
    for(;;) {
        char *last = *content;
        /*!re2c
            re2c:define:YYCTYPE  = char;
            re2c:define:YYCURSOR = *content;
            re2c:yyfill:enable   = 0;
            [\+\-]                { return *(*content-1); }
            [0-9]+                {
                                    int size = *content-last;
                                    yylval.number = (char *)malloc(size);
                                    sprintf(yylval.number, "%.*s", size, last);
                                    return TOK_NUMBER;
                                  }
            [ \t]+                { continue; }
            [\x00]                { return YYEOF; }
        */
    }

    return YYUNDEF;
}
```

For dump AST we need help function:
[ast.c](https://github.com/mrsuh/calc-parser/blob/main/src/ast.c)
```c
void dump_ast(parser_ast* ast, int indent) {
    for(int i = 0; i < indent; i++) {
        printf(" ");
    }

    printf("%s", ast->kind);

    if(ast->value != "") {
        printf(" \"%s\"", ast->value);
    }
    printf("\n");

    for(int i = 0; i < 2; i++) {
        if(ast->children[i] != NULL) {
            dump_ast(ast->children[i], indent + 2);
        }
    }
}
```

Now we can compile all files into one and test it:
```bash
bison --header -o parser.c parser.y
re2c lexer.l > lexer.c
gcc ast.c lexer.c parser.c -o parser
```

```bash
./parser "10 - 20 + 30"
OPERATION_PLUS
  OPERATION_MINUS
    NUMBER "10"
    NUMBER "20"
  NUMBER "30"
```

Cool. But I want to use it with `PHP`.
I need to compile a `shared` library:
```bash
gcc -fPIC -shared -I . -o library_linux.so ast.c lexer.c parser.c
```

It's time to include `library_linux.so` into `PHP` with `FFI`:
[parse.php](https://github.com/mrsuh/calc-parser/blob/main/bin/parse.php)
```php
<?php

$libc = \FFI::cdef('
typedef struct parser_ast {
    const char* kind;
    const char* value;
    struct parser_ast* children[2];
} parser_ast;
parser_ast* parse(char *content);
', __DIR__ . "/library_linux.so");

function dump($ast, int $indent = 0): void
{
    $node = $ast[0];
    printf("%s%s%s\n",
        (str_repeat(' ', $indent)),
        $node->kind,
        $node->value ? sprintf(" \"%s\"", $node->value) : ''
    );
    for ($i = 0; $i < 2; $i++) {
        if ($node->children[$i] !== null) {
            dump($node->children[$i], $indent + 2);
        }
    }
}

$ast = $libc->parse($argv[1]);
dump($ast);
```

```bash
php parse.php "10 - 20 + 30"
OPERATION_PLUS
  OPERATION_MINUS
    NUMBER "10"
    NUMBER "20"
  NUMBER "30"
```

And it works again!

I posted the source code on [GitHub](https://github.com/mrsuh/calc-parser).

Hope it will be useful for you!
