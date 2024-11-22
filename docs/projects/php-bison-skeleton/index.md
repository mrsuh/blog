# PHP Skeleton for Bison

[Bison](https://en.wikipedia.org/wiki/GNU_Bison) is a parser generator.
You can extend it with [PHP Skeleton](https://github.com/mrsuh/php-bison-skeleton) to generate PHP parsers.
For example, the PHP parser that can parse Nginx config:

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

By default, Bison works with C, C++, D, and Java. But you can use it with PHP by adding a [PHP skeleton](https://github.com/mrsuh/php-bison-skeleton).

## How It Works

![](./images/flow.png)

* grammar.y: a file where you define the syntax using BNF (Backus-Naur Form)
* skeleton.m4: a template file (written in M4) that helps create the parser
* parser.php: the parser file Bison generates, containing a Parser class

Bison reads your grammar.y file, processes it, and uses the skeleton.m4 file to generate the final parser file.

## About Bison

* [Read](/articles/2023/php-skeleton-for-bison/) about how Bison works
* [Read](/articles/2023/how-i-wrote-php-skeleton-for-bison/) about how the PHP parser was made and compare it with other PHP parsers
* [Read](/articles/2022/parsing-with-php-bison-and-re2c/) how to generate a C parser and use it with PHP FFI

## Examples

* [Math parser](/articles/ast-parser-with-php-and-bison/)
* [Nginx parser](/articles/2023/nginx-parser-with-php-and-bison/)
* [JSON parser](/articles/2023/json-parser-with-php-and-bison/)
