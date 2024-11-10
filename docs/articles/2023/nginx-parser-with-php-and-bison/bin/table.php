<?php

require_once __DIR__ . '/../vendor/autoload.php';

/**
server [258]
{ [123]
server_name [259]
domain.tld [260]
www.domain.tld [260]
; [59]
root [261]
/var/www/project/public [262]
; [59]
location [263]
/ [264]
{ [123]
try_files [283]
$uri [284]
/index.php$is_args$args [284]
; [59]
} [125]
} [125]

 */


$tableBuilder = new table\src\Builder();

$tableBuilder->headers(['word', 'token']);

$data = [
    ['server', 'LexerInterface::T_SERVER (258)'],
    ['{', 'ASCII (123)'],
    ['server_name', 'LexerInterface::T_SERVER_NAME (259)'],
    ['domain.tld', 'LexerInterface::T_SERVER_NAME_VALUE (260)'],
    ['www.domain.tld', 'LexerInterface::T_SERVER_NAME_VALUE (260)'],
    [';', 'ASCII (59)'],
    ['root', 'LexerInterface::T_SERVER_ROOT (261)'],
    ['/var/www/project/public', 'LexerInterface::T_SERVER_ROOT_PATH (262)'],
    [';', 'ASCII (59)'],
    ['location', 'LexerInterface::T_LOCATION (263)'],
    ['/', 'ASCII (264)'],
    ['{', 'ASCII (123)'],
    ['try_files', 'LexerInterface::T_TRY_FILES (283)'],
    ['$uri', 'LexerInterface::T_TRY_FILES_PATH (284)'],
    ['/index.php$is_args$args', 'LexerInterface::T_TRY_FILES_PATH (284)'],
    [';', 'ASCII (59)'],
    ['}', 'ASCII (125)'],
    ['}', 'ASCII (125)'],
    ['', 'LexerInterface::YYEOF (0)']
];

$tableBuilder->rows($data);

echo $tableBuilder->render();

