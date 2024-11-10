<?php

namespace 14 - kind - of - generics;

class Test
{
    public $var;
}

class Test2
{
    public $var;
}

class MyList
{
    /** @var Test[] */
    public array $data = [];
}

function all(MyList &$myList): array
{
    $arr = [];
    foreach ($myList->data as $item) {
        $arr[] = &$item;
    }
    return $arr;
}

$myList = new MyList();
$test = new Test();
$test->var = 1;
$myList->data = [$test];

foreach (all($myList) as &$item) {
    $item = new Test2();
}

var_dump($myList);
