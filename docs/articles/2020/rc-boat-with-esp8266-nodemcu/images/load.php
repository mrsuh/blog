<?php

$content = file_get_contents(__DIR__ . '/../index.md');
$index = 0;
$data =[];
foreach (explode(PHP_EOL, $content) as $line) {
    preg_match('/\!\[[^\]]*\]\(([^\)]+(png|jpeg|jpg|gif|webp))\)/', $line, $matches);

    if (!isset($matches[1])) {
        continue;
    }

    $url = $matches[1];
    
    $parts = explode('.', $url);

    $fileName = 'image-' . $index . '.' . end($parts);
    $index++;
    
    file_put_contents(
        __DIR__ . '/' . $fileName,
        file_get_contents($url)
    );

    $data[$url] = './images/' . $fileName;
    
    echo $url . PHP_EOL;
}


//var_dump($data);


file_put_contents(
    __DIR__ . '/../index-tmp.md',
    str_replace(array_keys($data), array_values($data), $content)
);
