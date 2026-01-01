<?php

require_once __DIR__ . '/../../../../../vendor/autoload.php';

echo '## AND' . PHP_EOL;
$tableBuilder = new \MaddHatter\MarkdownTable\Builder();
$tableBuilder->headers(['Input A', 'Input B', 'Output Y']);
$tableBuilder->rows([
    [0, 0, 0],
    [0, 1, 0],
    [1, 0, 0],
    [1, 1, 1],
]);
echo $tableBuilder->render();


echo '## NOT AND' . PHP_EOL;
$tableBuilder = new \MaddHatter\MarkdownTable\Builder();
$tableBuilder->headers(['Input A', 'Input B', 'Output Y']);
$tableBuilder->rows([
    [0, 0, 1],
    [0, 1, 1],
    [1, 0, 1],
    [1, 1, 0],
]);
echo $tableBuilder->render();


echo '## OR' . PHP_EOL;
$tableBuilder = new \MaddHatter\MarkdownTable\Builder();
$tableBuilder->headers(['Input A', 'Input B', 'Output Y']);
$tableBuilder->rows([
    [0, 0, 0],
    [0, 1, 1],
    [1, 0, 1],
    [1, 1, 1],
]);
echo $tableBuilder->render();


echo '## XOR' . PHP_EOL;
$tableBuilder = new \MaddHatter\MarkdownTable\Builder();
$tableBuilder->headers(['Input A', 'Input B', 'Output Y']);
$tableBuilder->rows([
    [0, 0, 0],
    [0, 1, 1],
    [1, 0, 1],
    [1, 1, 0],
]);
echo $tableBuilder->render();


echo '## HALF ADDER' . PHP_EOL;
$tableBuilder = new \MaddHatter\MarkdownTable\Builder();
$tableBuilder->headers(['Input A', 'Input B', 'Sum S', 'Carry C']);
$tableBuilder->rows([
    [0, 0, 0, 0],
    [0, 1, 1, 0],
    [1, 0, 1, 0],
    [1, 1, 0, 1],
]);
echo $tableBuilder->render();


