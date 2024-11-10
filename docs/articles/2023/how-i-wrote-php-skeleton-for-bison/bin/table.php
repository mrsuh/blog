<?php

require_once __DIR__ . '/../vendor/autoload.php';


$tableBuilder = new table\src\Builder();

$tableBuilder->headers(['software', 'type', 'lexer', 'parser']);

$data = [
    ['PHP', 'PHP engine', 're2c', 'Bison'],
    ['nikic/php-parser', 'PHP library', 'token_get_all()/re2c', 'php-yacc'],
    ['nikic/php-ast', 'PHP extension', 're2c', 'Bison'],
    ['mrsuh/php-ast', 'PHP FFI library', 're2c', 'Bison'],
];

$tableBuilder->rows($data);

echo $tableBuilder->render();

// ----------------------------------------------------


$tableBuilder = new table\src\Builder();

$tableBuilder->headers(['parser', 'file size', 'time (php7.4)', 'parser']);

$data = [
    ['PHP', 'PHP engine', 're2c', 'Bison'],
    ['nikic/php-parser', 'PHP library', 'token_get_all()/re2c', 'php-yacc'],
    ['nikic/php-ast', 'PHP extension', 're2c', 'Bison'],
    ['mrsuh/php-ast', 'PHP FFI library', 're2c', 'Bison'],
];

$tableBuilder->rows($data);

echo $tableBuilder->render();
