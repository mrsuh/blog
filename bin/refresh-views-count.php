<?php

use App\Integration\PostHog;

require_once __DIR__ . '/../vendor/autoload.php';

$data = [];

$postHog = new PostHog(file_get_contents(__DIR__ . '/../posthog.token'));
$articles = \App\Dataset\ArticleDataset::get();
foreach ($articles as $year => $list) {
    foreach ($list as $article) {
        echo $article->url . PHP_EOL;
        $data[$article->url] = $postHog->getViewsCount($article->url);
    }
}

file_put_contents(__DIR__ . '/../views.json', json_encode($data));
