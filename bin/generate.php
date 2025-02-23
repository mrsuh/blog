<?php

error_reporting(E_ALL ^ E_DEPRECATED);

use App\Dto\Article;
use App\Dto\Project;
use App\Dto\SiteMap;
use App\Dto\Rss;
use App\Generator\Html;
use App\Generator\Hash;
use App\Parser\Markdown;

require_once __DIR__ . '/../vendor/autoload.php';

$directory = __DIR__ . '/../docs';

$hash = new Hash(
    $directory,
    __DIR__ . '/../hash.json'
);
$hash->load();

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    'strict_variables' => true,
]);

$twig->addGlobal(
    'asset_version', 
    filemtime($directory . '/style.css')
);

$generator = new Html($directory, $twig);

$indexProjects = [];
$indexArticles = [];

$parser = new Markdown();
$parser->setBreaksEnabled(true);

$sitemap = [
    SiteMap::create('https://mrsuh.com', '2024-11-01'),
    SiteMap::create('https://mrsuh.com/articles/', '2024-11-13', 'daily'),
];

$rss = [];

$articles = [
    2024 => [
        Article::create(
            'SQLite Index Visualization: Search', 
            'Visualizes SQLite B-Tree index search operations, detailing internal navigation and data retrieval processes', 
            '/articles/2024/sqlite-index-visualization-search/', 
            '2024-12-01', 
            ["sqlite", "chart", "index", "visualization"],
        ),
        Article::create(
            'SQLite Index Visualization: Structure', 
            'Explores SQLite B-Tree index structure with visualizations, explaining on-disk and in-memory storage mechanisms', 
            '/articles/2024/sqlite-index-visualization-structure/', 
            '2024-11-05',
            ["sqlite", "chart", "index", "visualization"]
        ),
    ],
    2023 => [
        Article::create(
            'Few steps to make your docker image smaller', 
            'Offers practical tips for reducing Docker image size, enhancing container efficiency and performance', 
            '/articles/2023/few-steps-to-make-your-docker-image-smaller/', 
            '2023-02-20',
            ["php", "docker"]
        ),
        Article::create(
            'PHP Skeleton for Bison', 
            'Introduces a PHP skeleton for Bison parser generator, facilitating seamless parser integration into PHP projects',
            '/articles/2023/php-skeleton-for-bison/', 
            '2023-03-13',
            ["php", "bison", "skeleton"]
        ),
        Article::create(
            'AST parser with PHP and Bison', 
            'Guides on building an Abstract Syntax Tree (AST) parser using PHP and Bison, aiding in code analysis and manipulation',
            '/articles/2023/ast-parser-with-php-and-bison/', 
            '2023-03-19',
            ["php", "bison", "ast"]
        ),
        Article::create(
            'Nginx parser with PHP and Bison', 
            'Details creating an Nginx configuration parser with PHP and Bison, improving server configuration management',
            '/articles/2023/nginx-parser-with-php-and-bison/',
            '2023-03-27',
            ["php", "bison", "nginx"]
        ),
        Article::create(
            'JSON parser with PHP and Bison', 
            'Explains constructing a JSON parser using PHP and Bison, enabling efficient JSON data handling',
            '/articles/2023/json-parser-with-php-and-bison/', 
            '2023-04-03',
            ["php", "bison", "json"]
        ),
        Article::create(
            'How I Wrote PHP Skeleton For Bison', 
            'Shares insights into developing a PHP skeleton for Bison, offering a behind-the-scenes look at the process',
            '/articles/2023/how-i-wrote-php-skeleton-for-bison/',
            '2023-09-15',
            ["php", "bison", "skeleton"]
        ),
    ],
    2022 => [
        Article::create(
            'Generics implementation approaches', 
            'Discusses various methods for implementing generics in programming, providing simple examples for clarity',
            '/articles/2022/generics-implementation-approaches/', 
            '2022-02-08',
            ["php", "generics", "type erasure", "reification", "monomorphization"]
        ),
        Article::create(
            'Comparing PHP Collections', 
            'Compares different approaches to handling collections in PHP, evaluating type safety, IDE support, and performance',
            '/articles/2022/comparing-php-collections/', 
            '2022-03-22',
            ["php", "collections", "generics"]
        ),
        Article::create(
            'Telegram bot that monitors currency availability in Tinkoff ATMs', 
            'Describes creating a Telegram bot that tracks currency availability in Tinkoff ATMs, enhancing user convenience',
            '/articles/2022/telegram-bot-that-monitors-currency-availability-in-tinkoff-atms/', 
            '2022-04-02',
            ['bot']
        ),
        Article::create(
            'Parsing with PHP, Bison and re2c', 
            'Explores parsing techniques using PHP, Bison, and re2c, demonstrating how to build efficient parsers',
            '/articles/2022/parsing-with-php-bison-and-re2c/', 
            '2022-08-26',
            ["php", "bison", "re2c"]
        ),
        Article::create(
            'How PHP engine builds AST', 
            'Delves into the PHP engine\'s process of constructing Abstract Syntax Trees during code compilation',
            '/articles/2022/how-php-engine-builds-ast/', 
            '2022-09-05',
            ["php", "engine", "ast"]
        ),
    ],
    2021 => [
        Article::create(
            'PHP Generics. Right here. Right now', 
            'Introduces a solution for implementing generics in PHP, providing a practical approach for developers',
            '/articles/2021/php-generics-right-here-right-now/', 
            '2021-09-14',
            ["php", "generics", "php generics"]
        ),
    ],
    2020 => [
        Article::create(
            'How I migrated my hobby project to k8s', 
            'Shares the experience of migrating a personal project to Kubernetes (k8s), highlighting challenges and solutions',
            '/articles/2020/how-i-migrated-my-hobby-project-to-k8s/', 
            '2020-01-21',
            ['k8s']
        ),
        Article::create(
            'Looking for the most interesting articles on the site', 
            'Curates a list of engaging articles available on the website, spanning various topics',
            '/articles/2020/looking-for-the-most-interesting-articles-on-the-site/',
            '2020-09-17',
            ['research']
        ),
        Article::create(
            'RC car with ESP8266 NodeMCU and LEGO',
            'Details constructing a remote-controlled car using ESP8266 NodeMCU and LEGO components, blending electronics and creativity',
            '/articles/2020/rc-car-with-esp8266-nodemcu-and-lego/', 
            '2020-09-21',
            ["esp8266", "diy", "rc car", "lego"]
        ),
        Article::create(
            'RC boat with ESP8266 NodeMCU', 
            'Chronicles developing a remote-controlled boat powered by ESP8266 NodeMCU, from prototype to final model',
            '/articles/2020/rc-boat-with-esp8266-nodemcu/', 
            '2020-11-03',
            ["esp8266", "diy", "rc boat"]
        ),
    ],
    2019 => [
        Article::create(
            'Comparing PHP FPM, PHP PPM, Nginx Unit, ReactPHP, and RoadRunner',
            'Evaluates various PHP process managers and servers, comparing performance and features',
            '/articles/2019/comparing-php-fpm-php-ppm-nginx-unit-react-php-and-road-runner/', 
            '2019-01-14',
            ["tutorial", "development", "php-fpm", "php-ppm", "nginx", "nginx-unit", "react-php", "road runner"]
        ),
    ],
    2018 => [
        Article::create(
            'Mafia with Go, Vanila JS, and WebSockets', 
            'Describes developing an online version of the Mafia game using Go, Vanilla JavaScript, and WebSockets',
            '/articles/2018/mafia-with-go-vanila-js-and-websockets/', 
            '2018-10-05',
            ['go', 'javascript', 'websockets', 'gamedev']
        ),
    ],
    2017 => [
        Article::create(
            'Continuous delivery with Travis CI and Ansible',
            'Explains setting up continuous delivery pipelines using Travis CI and Ansible for automated deployments',
            '/articles/2017/continuous-delivery-with-travis-ci-and-ansible/', 
            '2017-04-03',
            ["tutorial", "development", "travis ci", "ansible", "continuous delivery"]
        ),
        Article::create(
            'Classifying housing ads: In search of the best solution',
            'Explores methods for classifying housing advertisements, seeking the most effective approach',
            '/articles/2017/classifying-housing-ads-in-search-of-the-best-solution/', 
            '2017-05-14',
            ['natural language processing', 'machine learning', 'tomita']
        ),
        Article::create(
            'Architecture of a service for collecting and classifying housing ads', 
            'Details designing and implementing a service dedicated to aggregating and categorizing housing ads',
            '/articles/2017/architecture-of-a-service-for-collecting-and-classifying-housing-ads/', 
            '2017-12-04',
            ['soa', 'microservices']
        ),
    ],
    2015 => [
        Article::create(
            'Migration from Symfony 2.0 to 2.6',
            'Shares the process and challenges encountered while upgrading a project from Symfony 2.0 to 2.6',
            '/articles/2015/migration-from-symfony-2-0-to-2-6/',
            '2015-05-20',
            ["php", "symfony"]
        ),
        Article::create(
            'SonarQube. Checking code quality', 
            'Introduces SonarQube as a tool for assessing code quality, outlining setup and usage',
            '/articles/2015/sonarqube-checking-code-quality/', 
            '2015-05-29',
            ['php', 'sonarqube']
        ),
        Article::create(
            'Nginx + Lua + Redis. Efficiently processing sessions and delivering data', 
            'Discusses combining Nginx, Lua, and Redis to handle sessions and data delivery efficiently',
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

        $articleFileContent = trim(implode(PHP_EOL, $lines));

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
                'content' => $parser->text($articleFileContent),
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

        $rss[] = Rss::create(
            'https://mrsuh.com' . $article->url,
            $article->date,
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
                
                $generator->generate(
                    $url,
                    'article/index.html.twig',
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
                            file_get_contents($headerFilePath) . file_get_contents($reportFilePath)
                        ),
                        'path' => $url,
                    ]
                );
            }
        }
        
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
    Project::create(
        'SocRent',
        'Service for parsing and classifying apartment rental ads.',
        '/projects/socrent/',
        ["socrent"],
    ),
    Project::create(
        'ESP32 mouse bot',
        'The bot emulates a Bluetooth mouse, allowing you to record macros and replay them multiple times.',
        '/projects/esp32-bluetooth-mouse-bot/',
        ["esp32", "bluetooth", "mouse", "clicker", "macros", "simulator"],
    ),
    Project::create(
        'ESP32 cam watcher',
        'This project helps me to observe my pot with pine\'s twig. I use ESP32 controller with a camera to take pictures and post it to Telegram channel.',
        '/projects/esp32-cam-watcher/',
        ["esp32", "bluetooth", "mouse", "clicker", "macros", "simulator"],
    ),
];
/** @var Project[] $projects */
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
