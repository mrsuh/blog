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
                    ["SQLite", "index search", "B-Tree", "query performance", "data locality", "database internals", "chart", "visualization"],
                    reposts: [
                        Repost::create('https://news.ycombinator.com/item?id=42294315'),
                        Repost::create('https://www.linkedin.com/feed/update/urn:li:share:7269268321370316800/'),
                        Repost::create('https://www.reddit.com/r/sqlite/comments/1h4qlb5/sqlite_index_visualization_search/'),
                        Repost::create('https://x.com/mrsuh6/status/1863503949845647506'),
                    ],
                    views: 0,
                    pdfVersion: true,
                    abstract: 'This article presents an in-depth analysis of how the SQLite database engine performs index-based searches. A custom visualization tool was developed to trace B-Tree traversal, key comparisons, and page reads during query execution. The work demonstrates how data locality, key ordering, and index depth influence search performance. Results are shown through visual diagrams and step-by-step query logs that highlight the internal behavior of SQLite indexes. The study concludes that visualization of query execution significantly improves understanding of index efficiency and can guide optimization in real-world database workloads.'
                ),
                Article::create(
                    'SQLite Index Visualization: Structure',
                    'Explores SQLite B-Tree index structure with visualizations, explaining on-disk and in-memory storage mechanisms',
                    '/articles/2024/sqlite-index-visualization-structure/',
                    '2024-11-05',
                    ["SQLite", "chart", "index structure", "B-Tree", "data storage", "page layout", "visualization"],
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
                    views: 0,
                    pdfVersion: true,
                    abstract: 'This work explores the internal structure of SQLite indexes at both the logical and physical levels. It visualizes B-Tree organization, page layout, and cell arrangement on disk to reveal how data is stored and navigated. The study uses experimental scripts and visual outputs to illustrate key and pointer organization within index pages. Findings show that even small variations in page structure affect read performance and space efficiency. The results provide a foundation for further educational and optimization research in database internals.'
                ),
            ],
            2023 => [
                Article::create(
                    'Few steps to make your docker image smaller',
                    'Offers practical tips for reducing Docker image size, enhancing container efficiency and performance',
                    '/articles/2023/few-steps-to-make-your-docker-image-smaller/',
                    '2023-02-20',
                    ["PHP", "Docker", "container image size", "optimization", "multi-stage build", "devops best practices"],
                    views: 170
                ),
                Article::create(
                    'PHP Skeleton for Bison',
                    'Introduces a PHP skeleton for Bison parser generator, facilitating seamless parser integration into PHP projects',
                    '/articles/2023/php-skeleton-for-bison/',
                    '2023-03-13',
                    ["PHP", "Bison", "parser generator", "language tooling", "scaffolding", "software architecture", "skeleton"],
                    views: 2_771,
                ),
                Article::create(
                    'AST parser with PHP and Bison',
                    'Guides on building an Abstract Syntax Tree (AST) parser using PHP and Bison, aiding in code analysis and manipulation',
                    '/articles/2023/ast-parser-with-php-and-bison/',
                    '2023-03-19',
                    ["PHP", "AST", "AST parser", "Bison", "syntax tree", "compiler construction", "language internals"],
                    views: 1_218,
                ),
                Article::create(
                    'Nginx parser with PHP and Bison',
                    'Details creating an Nginx configuration parser with PHP and Bison, improving server configuration management',
                    '/articles/2023/nginx-parser-with-php-and-bison/',
                    '2023-03-27',
                    ["Nginx", "PHP", "Bison", "parser", "web server configuration", "software tooling"],
                    views: 1_043,
                ),
                Article::create(
                    'JSON parser with PHP and Bison',
                    'Explains constructing a JSON parser using PHP and Bison, enabling efficient JSON data handling',
                    '/articles/2023/json-parser-with-php-and-bison/',
                    '2023-04-03',
                    ["JSON", "PHP", "Bison", "data serialization", "performance benchmarking", "language tooling"],
                    views: 788,
                ),
                Article::create(
                    'How I Wrote PHP Skeleton For Bison',
                    'Shares insights into developing a PHP skeleton for Bison, offering a behind-the-scenes look at the process',
                    '/articles/2023/how-i-wrote-php-skeleton-for-bison/',
                    '2023-09-15',
                    ["PHP", "Bison"],
                    views: -1,
                ),
            ],
            2022 => [
                Article::create(
                    'Generics implementation approaches',
                    'Discusses various methods for implementing generics in programming, providing simple examples for clarity',
                    '/articles/2022/generics-implementation-approaches/',
                    '2022-02-08',
                    ["generics", "type system", "monomorphization", "reification", "type erasure", "programming languages", "PHP", "Java", "C++"],
                    views: 923,
                    pdfVersion: true,
                    abstract: 'The paper surveys different implementation approaches to programming-language generics, including type erasure, reification, and monomorphization. Each technique is analyzed in terms of runtime behavior, memory usage, and compiler design complexity. Comparisons are drawn across languages such as C++, Java, and PHP. The study concludes that monomorphization offers the best trade-off for performance and type safety in dynamic languages like PHP, while maintaining readability and low runtime overhead.'
                ),
                Article::create(
                    'Comparing PHP Collections',
                    'Compares different approaches to handling collections in PHP, evaluating type safety, IDE support, and performance',
                    '/articles/2022/comparing-php-collections/',
                    '2022-03-22',
                    ["PHP", "collections library", "data structures", "performance comparison", "collections", "generics"],
                    views: 1_425,
                    pdfVersion: true,
                    abstract: 'This article compares several PHP collection libraries and their design patterns. It evaluates syntax, immutability, and runtime performance across practical examples. The analysis shows that library design strongly affects developer experience and memory usage, and highlights the trade-offs between simplicity and flexibility in data handling. The conclusions provide practical guidance for PHP developers choosing modern collection frameworks.'
                ),
                Article::create(
                    'Telegram bot that monitors currency availability in Tinkoff ATMs',
                    'Describes creating a Telegram bot that tracks currency availability in Tinkoff ATMs, enhancing user convenience',
                    '/articles/2022/telegram-bot-that-monitors-currency-availability-in-tinkoff-atms/',
                    '2022-04-02',
                    ["Telegram bot", "automation", "currency monitoring", "fintech", "PHP", "API"],
                    views: 5_600
                ),
                Article::create(
                    'Parsing with PHP, Bison and re2c',
                    'Explores parsing techniques using PHP, Bison, and re2c, demonstrating how to build efficient parsers',
                    '/articles/2022/parsing-with-php-bison-and-re2c/',
                    '2022-08-26',
                    ["Parsing", "PHP", "Bison", "re2c", "compiler tools", "language processing"],
                    views: 1_132,
                ),
                Article::create(
                    'How PHP engine builds AST',
                    'Delves into the PHP engine\'s process of constructing Abstract Syntax Trees during code compilation',
                    '/articles/2022/how-php-engine-builds-ast/',
                    '2022-09-05',
                    ["php", "engine", "ast", "PHP engine", "AST", "Zend Engine", "parsing pipeline", "compiler internals"],
                    reposts: [
                        Repost::create('https://news.ycombinator.com/item?id=32725170'),
                    ],
                    views: 2_302,
                    pdfVersion: true,
                    abstract: 'This study describes how the PHP engine parses source code and constructs its Abstract Syntax Tree (AST). It details lexical analysis, grammar parsing, and node creation within the Zend Engine. Annotated code fragments illustrate how syntax errors are detected and converted into structured nodes. The research concludes that understanding AST construction is critical for building efficient static analyzers, refactoring tools, and language extensions that interact with PHP internals.'
                ),
            ],
            2021 => [
                Article::create(
                    'PHP Generics. Right here. Right now',
                    'Introduces a solution for implementing generics in PHP, providing a practical approach for developers',
                    '/articles/2021/php-generics-right-here-right-now/',
                    '2021-09-14',
                    ["PHP", "generics", "type system", "language design", "implementation"],
                    views: 16_000
                ),
            ],
            2020 => [
                Article::create(
                    'How I migrated my hobby project to k8s',
                    'Shares the experience of migrating a personal project to Kubernetes (k8s), highlighting challenges and solutions',
                    '/articles/2020/how-i-migrated-my-hobby-project-to-k8s/',
                    '2020-01-21',
                    ["Kubernetes", "docker", "migration", "devops", "hobby project", "container orchestration"],
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
                    ["RC car", "ESP8266", "NodeMCU", "Lego", "IoT", "embedded systems", "DIY"],
                    views: 6_200
                ),
                Article::create(
                    'RC boat with ESP8266 NodeMCU',
                    'Chronicles developing a remote-controlled boat powered by ESP8266 NodeMCU, from prototype to final model',
                    '/articles/2020/rc-boat-with-esp8266-nodemcu/',
                    '2020-11-03',
                    ["RC boat", "ESP8266", "NodeMCU", "IoT", "embedded project", "DIY"],
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
                    ["PHP", "runtime performance", "PHP-FPM", "PHP-PPM", "Nginx Unit", "React PHP", "RoadRunner", "benchmark", "concurrency"],
                    views: 52_000,
                    pdfVersion: true,
                    abstract: 'This paper compares six PHP execution environments—PHP-FPM, PHP-PPM, Nginx Unit, ReactPHP, and RoadRunner—under identical test conditions. Benchmarks measure throughput, latency, and memory consumption using reproducible workloads. Results are summarized in detailed graphs and tables showing clear differences between synchronous and asynchronous runtimes. The study finds that event-driven frameworks significantly outperform traditional process-based models in concurrency and resource efficiency, offering a strong case for adopting asynchronous architectures in modern PHP applications.'
                ),
            ],
            2018 => [
                Article::create(
                    'Mafia with Go, Vanila JS, and WebSockets',
                    'Describes developing an online version of the Mafia game using Go, Vanilla JavaScript, and WebSockets',
                    '/articles/2018/mafia-with-go-vanila-js-and-websockets/',
                    '2018-10-05',
                    ["Go", "Vanilla JS", "WebSockets", "game development", "real-time application", "gamedev"],
                    views: 11_000
                ),
            ],
            2017 => [
                Article::create(
                    'Continuous delivery with Travis CI and Ansible',
                    'Explains setting up continuous delivery pipelines using Travis CI and Ansible for automated deployments',
                    '/articles/2017/continuous-delivery-with-travis-ci-and-ansible/',
                    '2017-04-03',
                    ["Continuous delivery", "Travis CI", "Ansible", "devops", "automation"],
                    views: 11_000
                ),
                Article::create(
                    'Classifying housing ads: In search of the best solution',
                    'Explores methods for classifying housing advertisements, seeking the most effective approach',
                    '/articles/2017/classifying-housing-ads-in-search-of-the-best-solution/',
                    '2017-05-14',
                    ["Machine learning", "housing ads", "classification", "real-estate data", "natural language processing", "tomita"],
                    views: 12_000,
                    pdfVersion: true,
                    abstract: 'This article presents a practical exploration of text classification techniques applied to real-world housing advertisements. The author describes three successive approaches to classifying rental listings: regular expressions, neural networks, and lexical (syntax) analysis. Each method was tested on a dataset of approximately 8,000 ads collected from social networks. The regular expression approach achieved 72.61% accuracy, while a neural network based on n-grams reached 77.13%. The final method, using the Tomita parser for lexical analysis, achieved 93.40% accuracy and additionally extracted structured information such as price, location, and contact details. The study demonstrates that for small datasets and high-accuracy requirements, custom lexical analyzers can outperform machine learning models. The article also describes the supporting infrastructure, including services for data collection, classification, and visualization, built with PHP, Go, and Node.js.',
                    doi: 'https://doi.org/10.5281/zenodo.17392704'
                ),
                Article::create(
                    'Architecture of a service for collecting and classifying housing ads',
                    'Details designing and implementing a service dedicated to aggregating and categorizing housing ads',
                    '/articles/2017/architecture-of-a-service-for-collecting-and-classifying-housing-ads/',
                    '2017-12-04',
                    ["Service architecture", "data collection", "real-estate ads", "machine learning", "software engineering"],
                    views: 9_300,
                ),
            ],
            2015 => [
                Article::create(
                    'Migration from Symfony 2.0 to 2.6',
                    'Shares the process and challenges encountered while upgrading a project from Symfony 2.0 to 2.6',
                    '/articles/2015/migration-from-symfony-2-0-to-2-6/',
                    '2015-05-20',
                    ["Symfony", "migration", "PHP", "framework upgrade", "software maintenance"],
                    views: 5_400
                ),
                Article::create(
                    'SonarQube. Checking code quality',
                    'Introduces SonarQube as a tool for assessing code quality, outlining setup and usage',
                    '/articles/2015/sonarqube-checking-code-quality/',
                    '2015-05-29',
                    ["SonarQube", "code quality", "static analysis", "software metrics", "PHP"],
                    views: 98_000
                ),
                Article::create(
                    'Nginx + Lua + Redis. Efficiently processing sessions and delivering data',
                    'Discusses combining Nginx, Lua, and Redis to handle sessions and data delivery efficiently',
                    '/articles/2015/nginx-lua-redis-efficiently-processing-sessions-and-delivering-data/',
                    '2015-11-11',
                    ["Nginx", "Lua", "Redis", "session processing", "high-performance web", "embedded scripting"],
                    views: 38_000
                ),
            ]
        ];
    }
}
