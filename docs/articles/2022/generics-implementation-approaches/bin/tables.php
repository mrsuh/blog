<?php

require_once __DIR__. '/../vendor/autoload.php';

use table\src\Builder;

$typErasureTableBuilder = new Builder();
$typErasureTableBuilder->headers(['', 'static analysis time', 'compile time', 'runtime']);
$typErasureTableBuilder->row(['type reflection', '+', '+', '-/+']);
$typErasureTableBuilder->row(['type checking', '+', '+', '-']);
echo $typErasureTableBuilder->render();
echo "\n";

$reificationTableBuilder = new Builder();
$reificationTableBuilder->headers(['', 'static analysis time', 'compile time', 'runtime']);
$reificationTableBuilder->row(['type reflection', '+', '+', '+']);
$reificationTableBuilder->row(['type checking', '+', '+', '+']);
echo $reificationTableBuilder->render();
echo "\n";


$monoTableBuilder = new Builder();
$monoTableBuilder->headers(['', 'static analysis time', 'compile time', 'runtime']);
$monoTableBuilder->row(['type reflection', '+', '+', '-']);
$monoTableBuilder->row(['type checking', '+', '+', '+']);
echo $monoTableBuilder->render();
echo "\n";
