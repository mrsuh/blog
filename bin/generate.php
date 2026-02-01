<?php

error_reporting(E_ALL ^ E_DEPRECATED);

use App\Dto\SiteMap;
use App\Dto\Rss;
use App\Generator\Html;
use App\Generator\Hash;
use App\Parser\Markdown;

require_once __DIR__ . '/../vendor/autoload.php';

ini_set('pcre.backtrack_limit', '50000000');

$directory = __DIR__ . '/../docs';

$hashFilePath = __DIR__ . '/../hash.json';
if(!in_array('--cache', $argv)) {
    if(is_file($hashFilePath)) {
        unlink($hashFilePath);
    }
    echo 'Cache removed' . PHP_EOL;
} else {
    echo 'Using cache' . PHP_EOL;
}

$generatePdf = in_array('--pdf', $argv);

$hash = new Hash($directory, $hashFilePath);
$hash->load();

$views = json_decode(file_get_contents(__DIR__ . '/../views.json'), true);

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    'strict_variables' => true,
]);

$twig->addGlobal('asset_version', filemtime($directory . '/style.css'));
$twig->addGlobal('orcid_url', 'https://orcid.org/0009-0005-3209-1773');

$generator = new Html($directory, $twig);
$pdfGenerator = new \App\Generator\Pdf($directory . '/pdf.css');

$indexProjects = [];
$indexArticles = [];

$parser = new Markdown();
$parser->setBreaksEnabled(true);

$sitemap = [
    SiteMap::create('https://mrsuh.com', '2024-11-01'),
    SiteMap::create('https://mrsuh.com/articles/', '2024-11-13', 'daily'),
];

$rss = [];

$articles = \App\Dataset\ArticleDataset::get();
foreach($articles as $year => $list) {
    foreach($list as $article) {
        
        if(str_contains($article->url, 'https://')) {
            continue;
        }
        
        if(!$article->active) {
            continue;
        }
        
        if(isset($views[$article->url]) && $article->views >= 0) {
            $article->views += $views[$article->url];
        }

        $articleFilePath = $directory . $article->url . 'index.md';
        if (!is_file($articleFilePath)) {
            echo $articleFilePath . PHP_EOL;
            continue;
        }
        
        $articleFileContent = file_get_contents($articleFilePath);
        $lines = explode(PHP_EOL, $articleFileContent);
        $quote = '';
        $origin = '';
        for($i = 0; $i <10; $i++) {

            if(empty($lines[$i])) {
                continue;
            }
            
            if(str_starts_with($lines[$i], '# ')) {
                unset($lines[$i]);
                continue;
            }

            if(str_starts_with($lines[$i], '[origin]')) {
                preg_match('/\[origin\](\S+)/', $articleFileContent, $matches);
                $origin = $matches[1] ?? '';
                unset($lines[$i]);
                continue;
            }

            if(str_starts_with($lines[$i], '>')) {
                $quote = $parser->text(str_replace('>', '', $lines[$i]));
                unset($lines[$i]);
                continue;
            }
            
            break;
        }

        $articleFileContentForPdf = trim(implode(PHP_EOL, $lines));
        foreach($lines as $i => $line) {
            if($line === '[pagebreak]') {
                unset($lines[$i]);
            }
        }
        $articleFileContentForHtml = trim(implode(PHP_EOL, $lines));

        $generator->generate(
            $article->url . 'index.html',
            'article/index.html.twig',
            [
                'title' => $article->title,
                'date' => $article->date,
                'origin' => $origin,
                'quote' => $quote,
                'description' => $article->description,
                'keywords' => $article->keywords,
                'page_name' => 'article',
                'content' => $parser->text($articleFileContentForHtml),
                'path' => $article->url,
                'views' => $article->views,
                'doi' => $article->doi,
            ]
        );
        
        echo '* ' . $article->url . 'index.html' . PHP_EOL;

        if($generatePdf && $article->pdfVersion) {

            $html = $generator->generateToString(
                'pdf/title.html.twig',
                [
                    'title' => $article->title,
                    'abstract' => $article->abstract,
                    'keywords' => $article->keywords,
                ]
            );
            
            $html .= '<pagebreak orientation="portrait">';
            
            $html .= $parser->text($articleFileContentForPdf);

            $pdfGenerator->generate(
                $directory  . $article->url . 'index.pdf',
                $directory  . $article->url,
                $html
            );
            
            echo $directory  . $article->url . 'index.pdf' . PHP_EOL;
        }
        
        if(count($indexArticles) < 2) {
            $indexArticles[] = $article;
        }
        
        $sitemap[] = SiteMap::create(
            'https://mrsuh.com' . $article->url,
            date('Y-m-d', filemtime($articleFilePath))
        );

        $rss[] = Rss::create(
            'https://mrsuh.com' . $article->url,
            $article->date->setTime(0,0,0),
            $article->title,
            $article->description
        );

        $reportDirectory = $directory . $article->url . 'reports';
        if(is_dir($reportDirectory)) {
            $headerFilePath = $reportDirectory . '/header.html';
            foreach(scandir($reportDirectory) as $reportFileName) {
                $reportFilePath = $reportDirectory . '/' . $reportFileName;
                if(!is_file($reportFilePath)) {
                    continue;
                }
                
                if(!str_contains($reportFileName, '.md')) {
                    continue;
                }

                $title = str_replace('.md', '', $reportFileName);
                $url = $article->url . 'reports/' . $title . '.html';
                $sitemap[] = SiteMap::create(
                    'https://mrsuh.com' . $url,
                    date('Y-m-d', filemtime($articleFilePath))
                );
                
                if(!$hash->isChanged($reportFilePath)) {
                    continue;
                }
                $hash->set($reportFilePath);
                
                $reportContent = file_get_contents($headerFilePath) . file_get_contents($reportFilePath);
                
                $generator->generate(
                    $url,
                    'report/index.html.twig',
                    [
                        'title' => $article->title . ' / Report ' . $title,
                        'description' => 'Report',
                        'keywords' => $article->keywords,
                        'page_name' => 'article',
                        'content' => str_replace(
                            '{{ content }}',
                            sprintf(
                                '<a href="%s">%s</a> / Report %s<br><br>',
                                $article->url,
                                $article->title,
                                $title
                            ),
                            $reportContent
                        ),
                        'path' => $url,
                    ]
                );

                if($generatePdf && $article->pdfVersion) {
                    $pdfGenerator->generate(
                        $reportDirectory  . '/report-' . str_replace('.md', '.pdf', $reportFileName),
                        $directory  . $article->url,
                        str_replace(
                            '{{ content }}',
                            sprintf('<h1>Report %s</h1><br><br>', $title),
                            $reportContent
                        )
                    );
                }
            }
        }
    }
}

$projects = \App\Dataset\ProjectDataset::get();
foreach ($projects as $project) {

    $projectFilePath = $directory . $project->url . 'index.md';
    
    foreach(['webp', 'png', 'jpg', 'jpeg'] as $extension) {
        $fileName = 'poster.' . $extension;
        if(is_file($directory .  $project->url . 'images/' . $fileName)) {
            $project->posterFileName = $fileName;
            break;
        }
    }
    
    $generator->generate(
        $project->url . 'index.html',
        'project/index.html.twig',
        [
            'title' => $project->title,
            'description' => $project->description,
            'keywords' => $project->keywords,
            'page_name' => 'project',
            'content' => $parser->text(file_get_contents($projectFilePath)),
            'path' => $project->url,
        ]
    );

    $sitemap[] = SiteMap::create(
        'https://mrsuh.com' . $project->url,
        date('Y-m-d', filemtime($projectFilePath))
    );
    
    if(count($indexProjects) < 2) {
        $indexProjects[] = $project;
    }
}

$sitemap[] = SiteMap::create(
    'https://mrsuh.com/projects/',
    '2026-01-29',
    'daily'
);

$generator->generate(
    'index.html',
    'index.html.twig',
    [
        'title' => 'Anton Sukhachev',
        'description' => 'Personal page',
        'keywords' => ['anton sukhachev', 'mrsuh', 'blog'],
        'page_name' => 'index',
        'projects' => $indexProjects,
        'articles' => $indexArticles,
        'path' => ''
    ]
);

$generator->generate(
    'projects/index.html',
    'project/list.html.twig',
    [
        'title' => 'Projects',
        'keywords' => ['anton sukhachev', 'mrsuh', 'blog', 'articles', 'posts', 'projects'],
        'page_name' => 'project',
        'list' => $projects,
        'path' => '/projects/',
    ]
);

$generator->generate(
    'articles/index.html',
    'article/list.html.twig', 
    [
        'title' => 'Articles',
        'keywords' => ['anton sukhachev', 'mrsuh', 'blog', 'articles', 'posts', 'projects'],
        'page_name' => 'article',
        'list' => $articles,
        'path' => '/articles/',
    ]
);

$generator->generate(
    'sitemap.xml',
    'sitemap.html.twig', 
    [
        'list' => $sitemap,
    ]
);

$generator->generate(
    'rss.xml',
    'rss.html.twig',
    [
        'title' => 'Anton Sukhachev',
        'description' => 'Personal page',
        'list' => $rss,
    ]
);

$hash->save();
