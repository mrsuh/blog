<?php

require_once __DIR__ . '/../vendor/autoload.php';

/**
{ [123]
array [258]
: [58]
[ [91]
string [258]
, [44]
99 [259]
, [44]
true [260]
, [44]
false [260]
, [44]
null [261]
] [93]
} [125]
 */


$tableBuilder = new table\src\Builder();

$tableBuilder->headers(['word', 'token']);

$data = [
    ['{', 'ASCII (123)'],
    ['"array"', 'LexerInterface::T_STRING (258)'],
    [':', 'ASCII (58)'],
    ['[', 'ASCII (91)'],
    ['"string"', 'LexerInterface::T_STRING (258)'],
    [',', 'ASCII (44)'],
    ['99', 'LexerInterface::T_NUMBER (259)'],
    [',', 'ASCII (44)'],
    ['true', 'LexerInterface::T_BOOL (260)'],
    [',', 'ASCII (44)'],
    ['false', 'LexerInterface::T_BOOL (260)'],
    [',', 'ASCII (44)'],
    ['null', 'LexerInterface::T_NULL (261)'],
    [',', 'ASCII (44)'],
    [']', 'ASCII (93)'],
    ['}', 'ASCII (125)'],
    ['', 'LexerInterface::YYEOF (0)']
];

$tableBuilder->rows($data);

echo $tableBuilder->render();

