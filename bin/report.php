<?php


use Symfony\Component\Process\Process;

require_once __DIR__ . '/../vendor/autoload.php';

$directory = __DIR__ . '/../docs';


$views = json_decode(file_get_contents(__DIR__ . '/../views.json'), true);
$notes = [];
$text = file_get_contents(__DIR__ . '/../notes.Txt');
foreach (explode(PHP_EOL, $text) as $line) {
    if(empty($line)) {
        continue;
    }
    
    [$url, $note] = explode('----', $line);
    
    $notes[trim($url)] = trim($note);
}


$data = [];

function download(string $url, string $filePath): void
{
    $process = new Process([
        'node',
        'bin/pdf-generator.js',
        $url,
        $filePath,
    ]);

    $process->mustRun();
}

$reportFilePath = __DIR__ . '/../report/README.md';

$reportDirectory = __DIR__ . '/../report';
$filesystem = new \Symfony\Component\Filesystem\Filesystem();

$articles = \App\Dataset\ArticleDataset::get();
foreach ($articles as $year => $list) {
    foreach ($list as $article) {
        $filePath = $directory . $article->url . 'index.md';
        $content = file_get_contents($filePath);
        $lines = explode(PHP_EOL, $content);
        $originalUrl = '';
        foreach ($lines as $line) {
            if (str_starts_with($line, '[origin]')) {
                $originalUrl = str_replace('[origin]', '', $line);
            }
        }
        
        $articleDirectory = $reportDirectory . preg_replace('/\/articles\/\d+/', '/articles', $article->url);
        $filesystem->mkdir($articleDirectory);
        
        $originalPath = $articleDirectory . 'Original.pdf';
        if(!is_file($originalPath) && !empty($originalUrl)) {
            echo $originalUrl . PHP_EOL;
            download($originalUrl, $originalPath);    
        }
        
        $translatedPath = $articleDirectory . 'Translated.pdf';
        if(!is_file($translatedPath)) {
            echo 'https://mrsuh.com' . $article->url . PHP_EOL;
            download('https://mrsuh.com' . $article->url, $translatedPath);    
        }
        
        $reportContent = '# ' . $article->title . PHP_EOL;
        $reportContent .= 'URL: ' . (!empty($originalUrl) ? $originalUrl : ('https://mrsuh.com' . $article->url)) . PHP_EOL;
        $reportContent .= 'Date: ' . $article->date->format('d F Y') . PHP_EOL;
        $reportContent .= 'Description: ' . $article->description . PHP_EOL;
        $reportContent .= 'PDF file: ' . rtrim(preg_replace('/\/articles\/\d+/', '/articles', $article->url), '/') . '.pdf' . PHP_EOL;
        $reportContent .= 'Views: ' . number_format($article->views + ($views[$article->url] ?? 0), 0, '.', ',') . PHP_EOL;
        $reportContent .= 'Notes: ' . ($notes[$article->url] ?? '') . PHP_EOL . PHP_EOL . PHP_EOL;
        
        file_put_contents($reportFilePath, $reportContent, FILE_APPEND);;
    }
}


