<?php

namespace App\Dataset;

use App\Dto\Article;
use App\Dto\Mention;
use App\Dto\Repost;

class ArticleDataset
{
    /**
     * @return Article[]
     */
    public static function get(): array
    {
        return [
            2024 => [
                Article::create(
                    'SQLite Index Visualization: Search',
                    'Visualizes SQLite B-Tree index search operations, detailing internal navigation and data retrieval processes',
                    '/articles/2024/sqlite-index-visualization-search/',
                    '2024-12-01',
                    ["sqlite", "chart", "index", "visualization"],
                    reposts: [
                        Repost::create('https://news.ycombinator.com/item?id=42294315'),
                        Repost::create('https://www.linkedin.com/feed/update/urn:li:share:7269268321370316800/'),
                        Repost::create('https://www.reddit.com/r/sqlite/comments/1h4qlb5/sqlite_index_visualization_search/'),
                        Repost::create('https://x.com/mrsuh6/status/1863503949845647506'),
                    ],
                    views: 0
                ),
                Article::create(
                    'SQLite Index Visualization: Structure',
                    'Explores SQLite B-Tree index structure with visualizations, explaining on-disk and in-memory storage mechanisms',
                    '/articles/2024/sqlite-index-visualization-structure/',
                    '2024-11-05',
                    ["sqlite", "chart", "index", "visualization"],
                    reposts: [
                        Repost::create('https://news.ycombinator.com/item?id=42134964'),
                        Repost::create('https://www.linkedin.com/feed/update/urn:li:share:7262817103374602240/'),
                        Repost::create('https://www.reddit.com/r/sqlite/comments/1gr1tgl/sqlite_index_visualization/'),
                        Repost::create('https://x.com/mrsuh6/status/1857048728688824363'),
                    ],
                    mentions: [
                        Mention::create(
                            'La veille des Ours',
                            'https://www.linkedin.com/pulse/la-veille-des-ours-n41-bearstech-ejmqf',
                            '2024-11-22',
                            'Visualiser la structure des index SQLite
Anton Sukhachev explore en profondeur la structure des index dans SQLite. Il examine comment les index sont stockés sur disque et en mémoire, en se concentrant sur la structure en B-Tree utilisée par SQLite. Sukhachev développe des outils pour analyser et visualiser ces structures, facilitant ainsi la compréhension de leur organisation interne.'
                        )
                    ],
                    views: 0
                ),
            ],
            2023 => [
                Article::create(
                    'Few steps to make your docker image smaller',
                    'Offers practical tips for reducing Docker image size, enhancing container efficiency and performance',
                    '/articles/2023/few-steps-to-make-your-docker-image-smaller/',
                    '2023-02-20',
                    ["php", "docker"],
                    views: 170
                ),
                Article::create(
                    'PHP Skeleton for Bison',
                    'Introduces a PHP skeleton for Bison parser generator, facilitating seamless parser integration into PHP projects',
                    '/articles/2023/php-skeleton-for-bison/',
                    '2023-03-13',
                    ["php", "bison", "skeleton"],
                    views: 2_771
                ),
                Article::create(
                    'AST parser with PHP and Bison',
                    'Guides on building an Abstract Syntax Tree (AST) parser using PHP and Bison, aiding in code analysis and manipulation',
                    '/articles/2023/ast-parser-with-php-and-bison/',
                    '2023-03-19',
                    ["php", "bison", "ast"],
                    views: 1_218
                ),
                Article::create(
                    'Nginx parser with PHP and Bison',
                    'Details creating an Nginx configuration parser with PHP and Bison, improving server configuration management',
                    '/articles/2023/nginx-parser-with-php-and-bison/',
                    '2023-03-27',
                    ["php", "bison", "nginx"],
                    views: 1_043
                ),
                Article::create(
                    'JSON parser with PHP and Bison',
                    'Explains constructing a JSON parser using PHP and Bison, enabling efficient JSON data handling',
                    '/articles/2023/json-parser-with-php-and-bison/',
                    '2023-04-03',
                    ["php", "bison", "json"],
                    views: 788
                ),
                Article::create(
                    'How I Wrote PHP Skeleton For Bison',
                    'Shares insights into developing a PHP skeleton for Bison, offering a behind-the-scenes look at the process',
                    '/articles/2023/how-i-wrote-php-skeleton-for-bison/',
                    '2023-09-15',
                    ["php", "bison", "skeleton"],
                    views: -1
                ),
            ],
            2022 => [
                Article::create(
                    'Generics implementation approaches',
                    'Discusses various methods for implementing generics in programming, providing simple examples for clarity',
                    '/articles/2022/generics-implementation-approaches/',
                    '2022-02-08',
                    ["php", "generics", "type erasure", "reification", "monomorphization"],
                    views: 923
                ),
                Article::create(
                    'Comparing PHP Collections',
                    'Compares different approaches to handling collections in PHP, evaluating type safety, IDE support, and performance',
                    '/articles/2022/comparing-php-collections/',
                    '2022-03-22',
                    ["php", "collections", "generics"],
                    views: 1_425
                ),
                Article::create(
                    'Telegram bot that monitors currency availability in Tinkoff ATMs',
                    'Describes creating a Telegram bot that tracks currency availability in Tinkoff ATMs, enhancing user convenience',
                    '/articles/2022/telegram-bot-that-monitors-currency-availability-in-tinkoff-atms/',
                    '2022-04-02',
                    ['bot'],
                    views: 5_600
                ),
                Article::create(
                    'Parsing with PHP, Bison and re2c',
                    'Explores parsing techniques using PHP, Bison, and re2c, demonstrating how to build efficient parsers',
                    '/articles/2022/parsing-with-php-bison-and-re2c/',
                    '2022-08-26',
                    ["php", "bison", "re2c"],
                    views: 1_132
                ),
                Article::create(
                    'How PHP engine builds AST',
                    'Delves into the PHP engine\'s process of constructing Abstract Syntax Trees during code compilation',
                    '/articles/2022/how-php-engine-builds-ast/',
                    '2022-09-05',
                    ["php", "engine", "ast"],
                    reposts: [
                        Repost::create('https://news.ycombinator.com/item?id=32725170'),
                    ],
                    views: 2_302
                ),
            ],
            2021 => [
                Article::create(
                    'PHP Generics. Right here. Right now',
                    'Introduces a solution for implementing generics in PHP, providing a practical approach for developers',
                    '/articles/2021/php-generics-right-here-right-now/',
                    '2021-09-14',
                    ["php", "generics", "php generics"],
                    views: 16_000
                ),
            ],
            2020 => [
                Article::create(
                    'How I migrated my hobby project to k8s',
                    'Shares the experience of migrating a personal project to Kubernetes (k8s), highlighting challenges and solutions',
                    '/articles/2020/how-i-migrated-my-hobby-project-to-k8s/',
                    '2020-01-21',
                    ['k8s'],
                    views: 8_700
                ),
                Article::create(
                    'Looking for the most interesting articles on the site',
                    'Curates a list of engaging articles available on the website, spanning various topics',
                    '/articles/2020/looking-for-the-most-interesting-articles-on-the-site/',
                    '2020-09-17',
                    ['research'],
                    views: 1_300
                ),
                Article::create(
                    'RC car with ESP8266 NodeMCU and LEGO',
                    'Details constructing a remote-controlled car using ESP8266 NodeMCU and LEGO components, blending electronics and creativity',
                    '/articles/2020/rc-car-with-esp8266-nodemcu-and-lego/',
                    '2020-09-21',
                    ["esp8266", "diy", "rc car", "lego"],
                    views: 6_200
                ),
                Article::create(
                    'RC boat with ESP8266 NodeMCU',
                    'Chronicles developing a remote-controlled boat powered by ESP8266 NodeMCU, from prototype to final model',
                    '/articles/2020/rc-boat-with-esp8266-nodemcu/',
                    '2020-11-03',
                    ["esp8266", "diy", "rc boat"],
                    reposts: [
                        Repost::create('https://www.reddit.com/r/esp8266/comments/1gsieks/rc_boat_with_esp8266/'),
                    ],
                    views: 19_000
                ),
            ],
            2019 => [
                Article::create(
                    'Comparing PHP FPM, PHP PPM, Nginx Unit, ReactPHP, and RoadRunner',
                    'Evaluates various PHP process managers and servers, comparing performance and features',
                    '/articles/2019/comparing-php-fpm-php-ppm-nginx-unit-react-php-and-road-runner/',
                    '2019-01-14',
                    ["tutorial", "development", "php-fpm", "php-ppm", "nginx", "nginx-unit", "react-php", "road runner"],
                    views: 52_000
                ),
            ],
            2018 => [
                Article::create(
                    'Mafia with Go, Vanila JS, and WebSockets',
                    'Describes developing an online version of the Mafia game using Go, Vanilla JavaScript, and WebSockets',
                    '/articles/2018/mafia-with-go-vanila-js-and-websockets/',
                    '2018-10-05',
                    ['go', 'javascript', 'websockets', 'gamedev'],
                    views: 11_000
                ),
            ],
            2017 => [
                Article::create(
                    'Continuous delivery with Travis CI and Ansible',
                    'Explains setting up continuous delivery pipelines using Travis CI and Ansible for automated deployments',
                    '/articles/2017/continuous-delivery-with-travis-ci-and-ansible/',
                    '2017-04-03',
                    ["tutorial", "development", "travis ci", "ansible", "continuous delivery"],
                    views: 11_000
                ),
                Article::create(
                    'Classifying housing ads: In search of the best solution',
                    'Explores methods for classifying housing advertisements, seeking the most effective approach',
                    '/articles/2017/classifying-housing-ads-in-search-of-the-best-solution/',
                    '2017-05-14',
                    ['natural language processing', 'machine learning', 'tomita'],
                    views: 12_000
                ),
                Article::create(
                    'Architecture of a service for collecting and classifying housing ads',
                    'Details designing and implementing a service dedicated to aggregating and categorizing housing ads',
                    '/articles/2017/architecture-of-a-service-for-collecting-and-classifying-housing-ads/',
                    '2017-12-04',
                    ['soa', 'microservices'],
                    views: 9_300
                ),
            ],
            2015 => [
                Article::create(
                    'Migration from Symfony 2.0 to 2.6',
                    'Shares the process and challenges encountered while upgrading a project from Symfony 2.0 to 2.6',
                    '/articles/2015/migration-from-symfony-2-0-to-2-6/',
                    '2015-05-20',
                    ["php", "symfony"],
                    views: 5_400
                ),
                Article::create(
                    'SonarQube. Checking code quality',
                    'Introduces SonarQube as a tool for assessing code quality, outlining setup and usage',
                    '/articles/2015/sonarqube-checking-code-quality/',
                    '2015-05-29',
                    ['php', 'sonarqube'],
                    views: 98_000
                ),
                Article::create(
                    'Nginx + Lua + Redis. Efficiently processing sessions and delivering data',
                    'Discusses combining Nginx, Lua, and Redis to handle sessions and data delivery efficiently',
                    '/articles/2015/nginx-lua-redis-efficiently-processing-sessions-and-delivering-data/',
                    '2015-11-11',
                    ['nginx', 'lua', 'redis', 'php', 'session'],
                    views: 38_000
                ),
            ]
        ];
    }
}
