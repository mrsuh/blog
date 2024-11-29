<?php

error_reporting(E_ALL ^ E_DEPRECATED);

use App\Dto\SiteMap;
use App\Dto\Article;
use App\Dto\Project;
use App\Html\Generator;
use App\Markdown\Parser;

require_once __DIR__ . '/../vendor/autoload.php';

$directory = __DIR__ . '/../docs';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    'strict_variables' => true,
]);

$twig->addGlobal(
    'asset_version', 
    filemtime($directory . '/style.css')
);

$generator = new Generator($directory, $twig);

$indexProjects = [];
$indexArticles = [];

$parser = new Parser();
$parser->setBreaksEnabled(true);

$sitemap = [
    SiteMap::create('https://mrsuh.com', '2024-11-01'),
    SiteMap::create('https://mrsuh.com/articles/', '2024-11-13', 'daily'),
];

$articles = [
    2024 => [
        Article::create(
            'SQLite Index Visualization: Search', 
            '/articles/2024/sqlite-index-visualization-search/', 
            '2024-11-15', 
            ["sqlite", "chart"],
            active: false
        ),
        Article::create(
            'SQLite Index Visualization: Structure', 
            '/articles/2024/sqlite-index-visualization-structure/', 
            '2024-11-05',
            ["sqlite", "chart"]
        ),
    ],
    2023 => [
        Article::create(
            'Few steps to make your docker image smaller', 
            '/articles/2023/few-steps-to-make-your-docker-image-smaller/', 
            '2023-02-20',
            ["php", "docker"]
        ),
        Article::create(
            'PHP Skeleton for Bison', 
            '/articles/2023/php-skeleton-for-bison/', 
            '2023-03-13',
            ["php", "bison", "skeleton"]
        ),
        Article::create(
            'AST parser with PHP and Bison', 
            '/articles/2023/ast-parser-with-php-and-bison/', 
            '2023-03-19',
            ["php", "bison", "ast"]
        ),
        Article::create(
            'Nginx parser with PHP and Bison', 
            '/articles/2023/nginx-parser-with-php-and-bison/',
            '2023-03-27',
            ["php", "bison", "nginx"]
        ),
        Article::create(
            'JSON parser with PHP and Bison', 
            '/articles/2023/json-parser-with-php-and-bison/', 
            '2023-04-03',
            ["php", "bison", "json"]
        ),
        Article::create(
            'How I Wrote PHP Skeleton For Bison', 
            '/articles/2023/how-i-wrote-php-skeleton-for-bison/',
            '2023-09-15',
            ["php", "bison", "skeleton"]
        ),
    ],
    2022 => [
        Article::create(
            'Generics implementation approaches', 
            '/articles/2022/generics-implementation-approaches/', 
            '2022-02-08',
            ["php", "generics", "type erasure", "reification", "monomorphization"]
        ),
        Article::create(
            'Comparing PHP Collections', 
            '/articles/2022/comparing-php-collections/', 
            '2022-03-22',
            ["php", "collections", "generics"]
        ),
        Article::create(
            'Telegram bot that monitors currency availability in Tinkoff ATMs', 
            'https://vc.ru/u/585016-anton-suhachev/393167-telegram-bot-kotoryi-sledit-za-valyutoi-v-bankomatah-tinkoff', 
            '2022-04-02',
        ),
        Article::create(
            'Parsing with PHP, Bison and re2c', 
            '/articles/2022/parsing-with-php-bison-and-re2c/', 
            '2022-08-26',
            ["php", "bison", "re2c"]
        ),
        Article::create(
            'How PHP engine builds AST', 
            '/articles/2022/how-php-engine-builds-ast/', 
            '2022-09-05',
            ["php", "engine", "ast"]
        ),
    ],
    2021 => [
        Article::create(
            'PHP Generics. Right here. Right now', 
            '/articles/2021/php-generics-right-here-right-now/', 
            '2021-09-14',
            ["php", "generics", "php generics"]
        ),
    ],
    2020 => [
        Article::create(
            'How I migrated my hobby project to k8s', 
            '/articles/2020/how-i-migrated-my-hobby-project-to-k8s/', 
            '2020-01-21',
            ['k8s']
        ),
        Article::create(
            'Looking for the most interesting articles on the site', 
            'https://vc.ru/dev/159230-ishem-samye-interesnye-stati-v-razdelah-na-saitah-vcru-tjournalru-i-dtfru',
            '2020-09-17',
        ),
        Article::create(
            'RC car with ESP8266 NodeMCU and LEGO',
            '/articles/2020/rc-car-with-esp8266-nodemcu-and-lego/', 
            '2020-09-21',
            ["esp8266", "diy", "rc car", "lego"]
        ),
        Article::create(
            'RC boat with ESP8266 NodeMCU', 
            '/articles/2020/rc-boat-with-esp8266-nodemcu/', 
            '2020-11-03',
            ["esp8266", "diy", "rc boat"]
        ),
    ],
    2019 => [
        Article::create(
            'Comparing PHP FPM, PHP PPM, Nginx Unit, ReactPHP, and RoadRunner', 
            '/articles/2019/comparing-php-fpm-php-ppm-nginx-unit-react-php-and-road-runner/', 
            '2019-01-14',
            ["tutorial", "development", "php-fpm", "php-ppm", "nginx", "nginx-unit", "react-php", "road runner"]
        ),
    ],
    2018 => [
        Article::create(
            'Mafia with Go, Vanila JS and WebSockets', 
            'https://habr.com/ru/articles/423821', 
            '2018-10-05',
        ),
    ],
    2017 => [
        Article::create(
            'Continuous delivery with Travis CI and Ansible',
            '/articles/2017/continuous-delivery-with-travis-ci-and-ansible/', 
            '2017-04-03',
            ["tutorial", "development", "travis ci", "ansible", "continuous delivery"]
        ),
        Article::create(
            'Classifying housing ads: In search of the best solution',
            '/articles/2017/classifying-housing-ads-in-search-of-the-best-solution/', 
            '2017-05-14',
            ['natural language processing', 'machine learning', 'tomita']
        ),
        Article::create(
            'Architecture of a service for collecting and classifying housing ads', 
            '/articles/2017/architecture-of-a-service-for-collecting-and-classifying-housing-ads/', 
            '2017-12-04',
            ['soa', 'microservices']
        ),
    ],
    2015 => [
        Article::create(
            'Migration from Symfony 2.0 to 2.6', 
            'https://habr.com/ru/articles/258403', 
            '2015-05-20',
        ),
        Article::create(
            'SonarQube. Checking code quality', 
            '/articles/2015/sonarqube-checking-code-quality/', 
            '2015-05-29',
            ['php', 'sonarqube']
        ),
        Article::create(
            'Nginx + Lua + Redis. Efficiently processing sessions and delivering data', 
            '/articles/2015/nginx-lua-redis-efficiently-processing-sessions-and-delivering-data/', 
            '2015-11-11',
            ['nginx', 'lua', 'redis', 'php', 'session']
        ),
    ]
];
foreach($articles as $year => $list) {
    foreach($list as $article) {
        
        if(str_contains($article->url, 'https://')) {
            continue;
        }
        
        if(!$article->active) {
            continue;
        }

        $articleFilePath = $directory . $article->url . 'index.md';
        if (!is_file($articleFilePath)) {
            echo $articleFilePath . PHP_EOL;
            continue;
        }

        $file = fopen($articleFilePath, 'r');
        $description = '';
        fgets($file);
        while (strlen($description) < 100) {
            $line = trim(strip_tags($parser->text(fgets($file))));
            if (empty($line)) {
                continue;
            }

            $description .= $line . ' ';
        }
        $description = substr($description, 0, 100) . '...';
        fclose($file);

        $generator->generate(
            $article->url . 'index.html',
            'article/index.html.twig',
            [
                'title' => $article->title,
                'description' => $description,
                'keywords' => $article->keywords,
                'page_name' => 'article',
                'content' => $parser->text(file_get_contents($articleFilePath)),
                'path' => $article->url,
            ]
        );
        
        if(count($indexArticles) < 2) {
            $indexArticles[] = $article;
        }
        
        $sitemap[] = SiteMap::create(
            'https://mrsuh.com' . $article->url,
            date('Y-m-d', filemtime($articleFilePath))
        );
    }
}

$projects = [
    Project::create(
        'PHP Generics',
        'Real PHP generics implemented in PHP with RFC-style syntax <b>Class&lt;T&gt;</b> and runtime type checking.',
        '/projects/php-generics/',
        ["php", "generics"],
    ),
    Project::create(
        'PHP Skeleton for Bison',
        'Bison is a parser generator. It can be used to create AST parsers for PHP, JSON, SQL, and more. By default, Bison supports C/C++/D/Java, but it can be extended using a PHP skeleton.',
        '/projects/php-bison-skeleton/',
        ["php", "bison", "skeleton"],
    ),
    Project::create(
        'ESP8266 RC boat',
        'RC boat powered by an ESP8266 microcontroller, featuring a Wi-Fi access point and controlled via WebSocket commands.',
        '/projects/esp8266-rc-boat/',
        ["php", "sizeof", "var_sizeof"],
    ),
    Project::create(
        'PHP var_sizeof()',
        'Function to get the actual size of any PHP variable in bytes. It calculates the size of internal PHP structures along with any additional allocated memory.',
        '/projects/php-var-sizeof/',
        ["php", "sizeof", "var_sizeof"],
    ),
];
/** @var Project[] $projects */
foreach ($projects as $project) {

    $projectFilePath = $directory . $project->url . 'index.md';
    
    if(!is_file($directory .  $project->url . 'images/' . $project->posterFileName)) {
        $project->posterFileName = 'poster.jpeg';
    }

    $generator->generate(
        $project->url . 'index.html',
        'project/index.html.twig',
        [
            'title' => $project->title,
            'description' => substr($project->description, 0, 100) . '...',
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
    date('Y-m-d', filemtime($directory . '/projects/index.html')),
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
