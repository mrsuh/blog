# Nginx parser with PHP and Bison

[origin]https://dev.to/mrsuh/nginx-parser-with-php-and-bison-1k5

> Read this [post](/articles/2023/php-skeleton-for-bison/) if you don't know what Bison is.

Today I'll try to parse Nginx config into AST.
I get the actual [Nginx config](https://symfony.com/doc/current/setup/web_server_configuration.html#nginx) from official Symfony documentation to test the parser.
`nginx.conf`
```php
server {
    server_name domain.tld www.domain.tld;
    root /var/www/project/public;

    location / {
        # try to serve file directly, fallback to index.php
        try_files $uri /index.php$is_args$args;
    }

    location /bundles {
        try_files $uri =404;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;

        # optionally set the value of the environment variables used in the application
        fastcgi_param APP_ENV prod;
        fastcgi_param APP_SECRET <app-secret-id>;
        fastcgi_param DATABASE_URL "mysql://db_user:db_pass@host:3306/db_name";
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;       
        internal;
    }

    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/project_error.log;
    access_log /var/log/nginx/project_access.log;
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
    │   └── parse.php # entry point to parse nginx configs
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
    /** @var array<string, mixed> */
    private array $attributes;
    /** @var Node[] */
    private array $children;

    public function __construct(string $name, array $attributes = [], array $children = [])
    {
        $this->name       = $name;
        $this->attributes = $attributes;
        $this->children   = $children;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function __toString(): string
    {
        $line = $this->name;
        if (!empty($this->attributes)) {
            $line .= ' {';
            foreach ($this->attributes as $key => $value) {
                $line .= sprintf(
                    " %s: '%s'",
                    $key,
                    is_array($value) ? implode(', ', $value) : $value
                );
            }
            $line .= ' }';
        }

        return $line;
    }
}
```

This time I'll use Doctrine lexer library. It can help to parse complex text.  
`src/Lexer.php`
```php
<?php

namespace App;

use Doctrine\Common\Lexer\AbstractLexer;

class Lexer extends AbstractLexer implements LexerInterface
{
    public function __construct($resource)
    {
        $this->setInput(stream_get_contents($resource));
        $this->moveNext();
    }

    protected function getCatchablePatterns(): array
    {
        return [';'];
    }

    protected function getNonCatchablePatterns(): array
    {
        return [' ','[\n]+','#[^\n]+']; // skip spaces, eol, and comments 
    }

    protected function getType(&$value): int
    {
        switch ($value) {
            case 'server':
                return LexerInterface::T_SERVER;
            case 'server_name':
                return LexerInterface::T_SERVER_NAME;

               ...
        }

        return ord($value);
    }

    public function yyerror(string $message): void
    {
        printf("%s\n", $message);
    }

    public function getLVal()
    {
        return $this->token->value;
    }

    public function yylex(): int
    {
        if (!$this->lookahead) {
            return LexerInterface::YYEOF;
        }

        $this->moveNext();

        return $this->token->type;
    }
}
```

For example, `Lexer` will translate the Nginx config below
```nginx
server {
    server_name domain.tld www.domain.tld;
    root /var/www/project/public;

    location / {
        # try to serve file directly, fallback to index.php
        try_files $uri /index.php$is_args$args;
    }
}
```

into this:

| word                    | token                                     |
|-------------------------|-------------------------------------------|
| server                  | LexerInterface::T_SERVER (258)            |
| {                       | ASCII (123)                               |
| server_name             | LexerInterface::T_SERVER_NAME (259)       |
| domain.tld              | LexerInterface::T_SERVER_NAME_VALUE (260) |
| www.domain.tld          | LexerInterface::T_SERVER_NAME_VALUE (260) |
| ;                       | ASCII (59)                                |
| root                    | LexerInterface::T_SERVER_ROOT (261)       |
| /var/www/project/public | LexerInterface::T_SERVER_ROOT_PATH (262)  |
| ;                       | ASCII (59)                                |
| location                | LexerInterface::T_LOCATION (263)          |
| /                       | ASCII (264)                               |
| {                       | ASCII (123)                               |
| try_files               | LexerInterface::T_TRY_FILES (283)         |
| $uri                    | LexerInterface::T_TRY_FILES_PATH (284)    |
| /index.php$is_args$args | LexerInterface::T_TRY_FILES_PATH (284)    |
| ;                       | ASCII (59)                                |
| }                       | ASCII (125)                               |
| }                       | ASCII (125)                               |
|                         | LexerInterface::YYEOF (0)                 |

Time to create `grammar.y` file and build `lib/parser.php`

We will use block `%code parser` to define variables and methods to store `AST` into the `Parser` class.
You can find full grammar file [here](https://github.com/mrsuh/php-bison-skeleton/blob/master/examples/nginx-ast/grammar.y).
`grammar.y`
```php
%define api.parser.class {Parser}
%define api.namespace {App}
%code parser {
    private Node $ast;
    public function setAst(Node $ast): void { $this->ast = $ast; }
    public function getAst(): Node { return $this->ast; }
}

%token T_SERVER
%token T_SERVER_NAME
%token T_SERVER_NAME_VALUE
%token T_SERVER_ROOT
%token T_SERVER_ROOT_PATH
...
%token T_TRY_FILES
%token T_TRY_FILES_PATH

%%
server:
  T_SERVER '{' server_body_list '}' { self::setAst(new Node('T_SERVER', [], $3)); }
;

server_name_values:
  T_SERVER_NAME_VALUE                     { $$ = [$1]; }
| server_name_values T_SERVER_NAME_VALUE  { $$ = $1; $$[] = $2; }
;

server_body:
  T_SERVER_NAME server_name_values ';'  { $$ = new Node('T_SERVER_NAME', ['names' => $2]); }
| T_SERVER_ROOT T_SERVER_ROOT_PATH ';'  { $$ = new Node('T_SERVER_ROOT', ['path' => $2]); }
| T_ERROR_LOG T_ERROR_LOG_PATH ';'      { $$ = new Node('T_ERROR_LOG', ['path' => $2]); }
| T_ACCESS_LOG T_ACCESS_LOG_PATH ';'    { $$ = new Node('T_ACCESS_LOG', ['path' => $2]); }
;
...
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
php bin/parse.php nginx.conf
.
├── T_SERVER
    ├── T_SERVER_NAME { names: 'domain.tld, www.domain.tld' }
    ├── T_SERVER_ROOT { path: '/var/www/project/public' }
    ├── T_LOCATION { regexp: '' path: '/' }
    │   └── T_TRY_FILES { paths: '$uri, /index.php$is_args$args' }
    ├── T_LOCATION { regexp: '' path: '/bundles' }
    │   └── T_TRY_FILES { paths: '$uri, =404' }
    ├── T_LOCATION { regexp: '~' path: '^/index\.php(/|$)' }
    │   ├── T_FAST_CGI_PATH { path: 'unix:/var/run/php/php-fpm.sock' }
    │   ├── T_FAST_CGI_SPLIT_PATH_INFO { path: '^(.+\.php)(/.*)$' }
    │   ├── T_INCLUDE { path: 'fastcgi_params' }
    │   ├── T_FAST_CGI_PARAM { APP_ENV: 'prod' }
    │   ├── T_FAST_CGI_PARAM { APP_SECRET: '<app-secret-id>' }
    │   ├── T_FAST_CGI_PARAM { DATABASE_URL: '"mysql://db_user:db_pass@host:3306/db_name"' }
    │   ├── T_FAST_CGI_PARAM { SCRIPT_FILENAME: '$realpath_root$fastcgi_script_name' }
    │   ├── T_FAST_CGI_PARAM { DOCUMENT_ROOT: '$realpath_root' }
    │   └── T_INTERNAL
    ├── T_LOCATION { regexp: '~' path: '\.php$' }
    │   └── T_RETURN { code: '404' body: '' }
    ├── T_ERROR_LOG { path: '/var/log/nginx/project_error.log' }
    └── T_ACCESS_LOG { path: '/var/log/nginx/project_access.log' }
```
It works!

You can get the parser source code [here](https://github.com/mrsuh/php-bison-skeleton/tree/master/examples/nginx-ast) and test it by yourself.

Some useful links:
* [PHP Skeleton for Bison](https://github.com/mrsuh/php-bison-skeleton/tree/master/examples)
* [AST parser with PHP and Bison](/articles/2023/ast-parser-with-php-and-bison/)
* [Bison docker image](https://github.com/mrsuh/docker-bison)
* [Parsing with PHP, Bison, and re2c](/articles/2022/parsing-with-php-bison-and-re2c/)
