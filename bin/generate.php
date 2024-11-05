<?php

require_once __DIR__ . '/../vendor/autoload.php';

$parser = new Parsedown();
$parser->setBreaksEnabled(true);

$template = file_get_contents(__DIR__ . '/../src/template.html');

file_put_contents(
    __DIR__ . '/../docs/index.html',
    str_replace('{{ content }}', $parser->text(file_get_contents(__DIR__ . '/../src/index.md')), $template)
);

file_put_contents(
    __DIR__ . '/../docs/articles.html',
    str_replace('{{ content }}', $parser->text(file_get_contents(__DIR__ . '/../src/articles.md')), $template)
);
