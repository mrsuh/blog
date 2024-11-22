<?php

require_once __DIR__ . '/../vendor/autoload.php';

class MyParserdown extends \Parsedown
{
    protected function inlineImage($Excerpt)
    {
        $data = parent::inlineImage($Excerpt);
        if (!is_array($data)) {
            return;
        }

        $data['element']['attributes']['class'] = 'img-fluid mx-auto d-block rounded img-size';

        $Inline = array(
            'extent' => $data['extent'],
            'element' => array(
                'name' => 'a',
                'handler' => 'element',
                'attributes' => array(
                    'href' => $data['element']['attributes']['src'],
                ),
                'text' => $data['element']
            ),
        );

        return $Inline;
    }

    protected function blockFencedCode($Line)
    {
        $data = parent::blockFencedCode($Line);
        if (!is_array($data)) {
            return;
        }

        $element = &$data['element']['text'];
        if (!isset($element['attributes'])) {
            $element['attributes'] = ['class' => ''];
        }

        $element['attributes']['class'] .= ' rounded';

        return $data;
    }
    
    protected function blockTable($Line, array $Block = null)
    {
        $data = parent::blockTable($Line, $Block);
        if (!is_array($data)) {
            return;
        }

        $data['element']['attributes'] = [
            'class' => 'table table-bordered'
        ];

        $data['element']['text'][0]['attributes'] = [
            'class' => 'table-secondary'
        ];

        return $data;
    }

    protected function blockHeader($Line)
    {
        $data = parent::blockHeader($Line);
        if (!is_array($data)) {
            return;
        }

        $name = $data['element']['name'];
        if (in_array($name, ['h1'])) {
            return $data;
        }

        $text = $data['element']['text'];

        $id = $name . '-' . str_replace(' ', '-', strtolower($text));

        $Inline = [
            'element' => [
                'name' => 'a',
                'handler' => 'element',
                'attributes' => [
                    'href' => '#' . $id,
                    'id' => $id,
                    'class' => 'text-decoration-none text-reset',
                ],
                'text' => $data['element']
            ],
        ];

        return $Inline;
    }

    protected function inlineLink($Excerpt) {
        $data = parent::inlineLink($Excerpt);
        if (!is_array($data)) {
            return;
        }
        
        $link = $data['element']['attributes']['href'];
        
        if(strpos($link, 'http') !== false) {
            $data['element']['attributes']['target'] = '_blank';
        }

        $data['element']['attributes']['class'] = 'link-primary link-underline-opacity-0 link-underline-opacity-100-hover';
        
        return $data;
    }

    protected function blockQuote($Line) {
        $data = parent::blockQuote($Line);
        if (!is_array($data)) {
            return;
        }
        
        $data['element']['attributes'] = [
            'class' => 'text-muted link-secondary quote'
        ];
        
        return $data;
    }

    protected function blockQuoteContinue($Line, array $Block)
    {
        $data = parent::blockQuoteContinue($Line, $Block);
        if (!is_array($data)) {
            return;
        }

        return $data;
    }
}

$parser = new MyParserdown();

$parser->setBreaksEnabled(true);

$indexContent = file_get_contents(__DIR__ . '/../src/index.md');

$template = file_get_contents(__DIR__ . '/../src/template.html');

class Article
{
    public string $name;
    public string $url;
    public string $date;
    public array $keywords;
    public bool $active;

    public static function create(
        string $name,
        string $url,
        string $date,
        array $keywords = [],
        bool $active = true,
    ): self
    {
        $self = new self();
        $self->name = $name;
        $self->url = $url;
        $self->date = $date;
        $self->keywords = $keywords;
        $self->active = $active;

        return $self;
    }
}

class Project
{
    public string $title;
    public string $description;
    public string $url;
    public array $keywords;

    public static function create(
        string $title,
        string $description,
        string $url,
        array $keywords = [],
    ): self
    {
        $self = new self();
        $self->title = $title;
        $self->description = $description;
        $self->url = $url;
        $self->keywords = $keywords;

        return $self;
    }
}

class SiteMapEntity
{
    public string $url;
    public string $date;
    public string $changefreq;

    public static function create(
        string $url,
        string $date,
        string $changefreq = 'weekly',
    ): self
    {
        $self = new self();
        $self->url = $url;
        $self->date = $date;
        $self->changefreq = $changefreq;

        return $self;
    }
}

$sitemap = [
    SiteMapEntity::create('https://mrsuh.com', '2024-11-01'),
    SiteMapEntity::create('https://mrsuh.com/articles/', '2024-11-13', 'daily'),
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
            'https://habr.com/ru/articles/259149', 
            '2015-05-29',
        ),
        Article::create(
            'Nginx + Lua + Redis. Efficiently processing sessions and delivering data', 
            '/articles/2015/nginx-lua-redis-efficiently-processing-sessions-and-delivering-data/', 
            '2015-11-11',
            ['nginx', 'lua', 'redis', 'php', 'session']
        ),
    ]
];

$content = '';
$indexContent .= '</br>' . PHP_EOL;
$indexContent .= '<h4>Recent Articles</h4>' . PHP_EOL;
$indexContent .= '<div class="row">' . PHP_EOL;
$articleIndex = 0;
foreach ($articles as $year => $list) {
    $content .= '<h4>' . $year . '</h4>' . PHP_EOL;
    $content .= '<div class="row">' . PHP_EOL;
    /** @var Article $article */
    foreach ($list as $index => $article) {
        
        if(!$article->active) {
            continue;
        }
        
        $articleContent = '';
        
        $isSamePage = strpos($article->url, 'http') === false;
        $articleContent .= sprintf(
            '<div class="col-10"><a href="%s" %s class="link-primary link-underline-opacity-0 link-underline-opacity-100-hover">%s</a></div>', 
            $article->url,
            $isSamePage ? '' : 'target="_blank"',
            $article->name
        );
        $articleContent .= sprintf('<div class="col-2 text-end list-date">%s</div>', \DateTime::createFromFormat('Y-m-d', $article->date)->format('M j'));
        $articleContent .= '<hr class="list"/>' . PHP_EOL;
        
        $content .= $articleContent;
        if($articleIndex < 2) {
            $indexContent .= $articleContent;    
        }
        $articleIndex++;
    }

    $content .= '</div>' . PHP_EOL;
    $content .= '<br/>' . PHP_EOL;
}

$indexContent .= '</div>' . PHP_EOL;
$indexContent .= '<br/>' . PHP_EOL;

file_put_contents(
    __DIR__ . '/../docs/articles/index.html',
    str_replace(
        [
            '{{ content }}',
            '{{ title }}',
            '{{ description }}',
            '{{ path }}',
            '{{ scripts }}',
            '{{ keywords }}',
            '{{ menu_main_class }}',
            '{{ menu_article_class }}',
            '{{ menu_project_class }}',
        ],
        [
            $content,
            'Articles',
            '',
            '/articles',
            '',
            'anton sukhachev, mrsuh, blog, articles, posts',
            'link-underline-opacity-0 link-underline-opacity-100-hover',
            'link-underline-opacity-100',
            'link-underline-opacity-0 link-underline-opacity-100-hover',
        ],
        $template
    )
);


$directory = __DIR__ . '/../docs';
foreach($articles as $year => $list) {
    foreach($list as $article) {
        
        if(strpos($article->url, 'https://') !== false) {
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
            $line = htmlspecialchars(trim(strip_tags($parser->text(fgets($file)))));
            if (empty($line)) {
                continue;
            }

            $description .= $line . ' ';
        }
        $description = substr($description, 0, 100) . '...';
        fclose($file);

        $keywords = ["development"];
        $keywordsFilePath = $directory . $article->url . 'keywords.json';
        if(is_file($keywordsFilePath)) {
            $keywords = json_decode(file_get_contents($keywordsFilePath), true);
        }

        file_put_contents(
            $directory . $article->url . 'index.html',
            str_replace(
                [
                    '{{ content }}',
                    '{{ title }}',
                    '{{ description }}',
                    '{{ path }}',
                    '{{ scripts }}',
                    '{{ keywords }}',
                    '{{ menu_main_class }}',
                    '{{ menu_article_class }}',
                    '{{ menu_project_class }}',
                ],
                [
                    $parser->text(file_get_contents($articleFilePath)),
                    $article->name,
                    $description,
                    $article->url,
                    '<link rel="stylesheet" href="/highlight.github-dark-dimmed.min.css">
<script src="/highlight.min.js"></script>
<script>hljs.highlightAll();</script>
',
                    implode(', ', $article->keywords),
                    'link-underline-opacity-0 link-underline-opacity-100-hover',
                    'link-underline-opacity-100',
                    'link-underline-opacity-0 link-underline-opacity-100-hover',
                ],
                $template,
            )
        );

        $sitemap[] = SiteMapEntity::create(
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
];

$indexContent .= '<h4>Recent Projects</h4>' . PHP_EOL;

$projectIndex = 0;
$content = '';
/** @var Project $project */
foreach ($projects as $project) {

    $projectContent = '';

    $projectContent .= sprintf('
    <div class="card mb-3" style="max-width: 100%%;">
            <div class="row g-0">
                <div class="col-md-4">
                    <img src="%s"class="img-fluid rounded-start">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title"><a href="%s">%s</a></h5>
                        <p class="card-text">%s</p>
                    </div>
                </div>
            </div>
    </div>
    ',
    $project->url . 'images/poster.png',
    $project->url,
    $project->title,
        $project->description
    );
    
    $content .= $projectContent;
    
    if($projectIndex < 2) {
        $indexContent .= $projectContent;
    }
    $projectIndex++;

    $projectFilePath = $directory . $project->url . 'index.md';

    file_put_contents(
        $directory . $project->url . 'index.html',
        str_replace(
            [
                '{{ content }}',
                '{{ title }}',
                '{{ description }}',
                '{{ path }}',
                '{{ scripts }}',
                '{{ keywords }}',
                '{{ menu_main_class }}',
                '{{ menu_article_class }}',
                '{{ menu_project_class }}',
            ],
            [
                $parser->text(file_get_contents($projectFilePath)),
                $project->title,
                substr($project->description, 0, 100) . '...',
                $project->url,
                '<link rel="stylesheet" href="/highlight.github-dark-dimmed.min.css">
<script src="/highlight.min.js"></script>
<script>hljs.highlightAll();</script>
',
                implode(', ', $article->keywords),
                'link-underline-opacity-0 link-underline-opacity-100-hover',
                'link-underline-opacity-0 link-underline-opacity-100-hover',
                'link-underline-opacity-100',
            ],
            $template,
        )
    );

    $sitemap[] = SiteMapEntity::create(
        'https://mrsuh.com' . $project->url,
        date('Y-m-d', filemtime($projectFilePath))
    );
}


file_put_contents(
    __DIR__ . '/../docs/projects/index.html',
    str_replace(
        [
            '{{ content }}',
            '{{ title }}',
            '{{ description }}',
            '{{ path }}',
            '{{ scripts }}',
            '{{ keywords }}',
            '{{ menu_main_class }}',
            '{{ menu_article_class }}',
            '{{ menu_project_class }}',
        ],
        [
            $content,
            'Projects',
            '',
            '/projects',
            '',
            'anton sukhachev, mrsuh, blog, articles, posts, projects',
            'link-underline-opacity-0 link-underline-opacity-100-hover',
            'link-underline-opacity-0 link-underline-opacity-100-hover',
            'link-underline-opacity-100',
        ],
        $template
    )
);

$sitemap[] = SiteMapEntity::create(
    'https://mrsuh.com/projects/',
    date('Y-m-d', filemtime(__DIR__ . '/../docs/projects/index.html')),
    'daily'
);


$sitemapContent = '
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

foreach($sitemap as $entity) {
    $sitemapContent .= '  <url>' . PHP_EOL;
    $sitemapContent .= '    <loc>' . $entity->url . '</loc>' . PHP_EOL;
    $sitemapContent .= '    <lastmod>' . $entity->date . '</lastmod>' . PHP_EOL;
    $sitemapContent .= '    <changefreq>' . $entity->changefreq . '</changefreq>' . PHP_EOL;
    $sitemapContent .= '    <priority>1</priority>' . PHP_EOL;
    $sitemapContent .= '  </url>' . PHP_EOL;
}
$sitemapContent .= '</urlset>';

file_put_contents(__DIR__ . '/../docs/sitemap.xml', trim($sitemapContent));


file_put_contents(__DIR__ . '/../docs/index.html', str_replace(
    [
        '{{ content }}',
        '{{ title }}',
        '{{ description }}',
        '{{ path }}',
        '{{ scripts }}',
        '{{ keywords }}',
        '{{ menu_main_class }}',
        '{{ menu_article_class }}',
        '{{ menu_project_class }}',
    ],
    [
        $parser->text($indexContent),
        'Anton Sukhachev',
        'Personal page',
        '',
        '',
        'anton sukhachev, mrsuh, blog',
        'link-underline-opacity-100',
        'link-underline-opacity-0 link-underline-opacity-100-hover',
        'link-underline-opacity-0 link-underline-opacity-100-hover',
    ],
    $template
));
