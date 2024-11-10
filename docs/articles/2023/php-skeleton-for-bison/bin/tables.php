<?php

require_once __DIR__ . '/vendor/autoload.php';

$tableBuilder = new table\src\Builder();

$tableBuilder->headers(['software', 'grammar.y']);

$data = [
    ['PHP', 'https://github.com/php/php-src/blob/master/Zend/zend_language_parser.y'],
    ['Bash', 'https://git.savannah.gnu.org/cgit/bash.git/tree/parse.y'],
    ['Ruby', 'https://github.com/ruby/ruby/blob/master/parse.y'],
    ['MySQL', 'https://github.com/mysql/mysql-server/blob/8.0/sql/sql_yacc.yy'],
    ['PostgreSQL', 'https://github.com/postgres/postgres/blob/master/src/backend/parser/gram.y'],
    ['CMake', 'https://github.com/Kitware/CMake/blob/master/Source/LexerParser/cmExprParser.y'],
];

$tableBuilder->rows($data);

echo $tableBuilder->render();

echo PHP_EOL;

$tableBuilder = new table\src\Builder();

$tableBuilder->headers(['word', 'token', 'value']);

$data = [
    ['10', 'LexerInterface::T_NUMBER (258)', '10'],
    ['+', 'ASCII (43)', ''],
    ['20', 'LexerInterface::T_NUMBER (258)', '20'],
    ['-', 'ASCII (45)', ''],
    ['30', 'LexerInterface::T_NUMBER (258)', '30'],
    ['', 'LexerInterface::YYEOF (0)', '']
];

$tableBuilder->rows($data);

echo $tableBuilder->render();
