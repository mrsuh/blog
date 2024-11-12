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

        $data['element']['attributes']['class'] = 'img-fluid mx-auto d-block rounded img-max-height';

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
        if (!in_array($name, ['h2', 'h3'])) {
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

$template = file_get_contents(__DIR__ . '/../src/template.html');

file_put_contents(
    __DIR__ . '/../docs/index.html',
    str_replace(
        [
            '{{ content }}',
            '{{ title }}',
            '{{ description }}',
            '{{ path }}',
            '{{ scripts }}',
        ],
        [
            $parser->text(file_get_contents(__DIR__ . '/../src/index.md')),
            'Anton Sukhachev',
            'Personal page',
            '',
            '',
        ],
        $template
    )
);

class Article
{
    public string $name;
    public string $url;
    public string $date;

    public static function create(
        string $name,
        string $url,
        string $date
    ): self
    {
        $self = new self();
        $self->name = $name;
        $self->url = $url;
        $self->date = $date;

        return $self;
    }
}

class SiteMapEntity
{
    public string $url;
    public string $date;

    public static function create(
        string $url,
        string $date
    ): self
    {
        $self = new self();
        $self->url = $url;
        $self->date = $date;

        return $self;
    }
}

$sitemap = [
    SiteMapEntity::create('https://mrsuh.com', '2024-11-01'),
    SiteMapEntity::create('https://mrsuh.com/articles/', '2024-11-01'),
];

$articles = [
    2024 => [
        Article::create("SQLite Index Visualization: Structure", "/articles/2024/sqlite-index-visualization-structure/", "Nov 5"),
        Article::create("SQLite Index Visualization: Search", "/articles/2024/sqlite-index-visualization-search/", "Nov 15"),
    ],
    2023 => [
        Article::create("Few steps to make your docker image smaller", "/articles/2023/few-steps-to-make-your-docker-image-smaller/", "Feb 20"),
        Article::create("PHP Skeleton for Bison", "/articles/2023/php-skeleton-for-bison/", "Mar 13"),
        Article::create("AST parser with PHP and Bison", "/articles/2023/ast-parser-with-php-and-bison/", "Mar 19"),
        Article::create("Nginx parser with PHP and Bison", "/articles/2023/nginx-parser-with-php-and-bison/", "Mar 27"),
        Article::create("JSON parser with PHP and Bison", "/articles/2023/json-parser-with-php-and-bison/", "Apr 3"),
        Article::create("How I Wrote PHP Skeleton For Bison", "/articles/2023/how-i-wrote-php-skeleton-for-bison/", "Sep 15"),
    ],
    2022 => [
        Article::create("Generics implementation approaches", "/articles/2022/generics-implementation-approaches/", "Feb 8"),
        Article::create("Comparing PHP Collections", "/articles/2022/comparing-php-collections/", "Mar 22"),
        Article::create("Telegram bot that monitors currency availability in Tinkoff ATMs", "https://vc.ru/u/585016-anton-suhachev/393167-telegram-bot-kotoryi-sledit-za-valyutoi-v-bankomatah-tinkoff", "Apr 02"),
        Article::create("Parsing with PHP, Bison and re2c", "/articles/2022/parsing-with-php-bison-and-re2c/", "Aug 26"),
        Article::create("How PHP engine builds AST", "/articles/2022/how-php-engine-builds-ast/", "Sep 5"),
        Article::create("PHP generics", "https://phprussia.ru/moscow/2022/abstracts/9165", "Nov 25"),
    ],
    2021 => [
        Article::create("PHP Generics . Right here . Right now", "/articles/2021/php-generics-right-here-right-now/", "Sep 14"),
    ],
    2020 => [
        Article::create("How I migrated my hobby project to k8s", "https://habr.com/ru/articles/484528", "Jan 21"),
        Article::create("Looking for the most interesting articles on the site", "https://vc.ru/dev/159230-ishem-samye-interesnye-stati-v-razdelah-na-saitah-vcru-tjournalru-i-dtfru", "Sep 17"),
        Article::create("RC - car with ESP8266 NodeMCU and LEGO", "https://vc.ru/dev/160142-rc-mashinka-iz-esp8266-nodemcu-i-lego", "Sep 21"),
        Article::create("RC - boat with ESP8266 NodeMCU", "https://habr.com/ru/articles/513482", "Nov 3"),
    ],
    2019 => [
        Article::create("Comparing PHP FPM, PHP PPM, Nginx Unit, ReactPHP, and RoadRunner", "https://habr.com/ru/articles/431818", "Jan 14"),
    ],
    2018 => [
        Article::create("Mafia with Go, Vanila JS and WebSockets", "https://habr.com/ru/articles/423821", "Oct 5"),
    ],
    2017 => [
        Article::create("Continuous delivery with Travis CI and Ansible", "https://habr.com/ru/articles/325438", "Apr 3"),
        Article::create("Classifying listings from social networks: In search of the best solution", "https://habr.com/ru/articles/328282", "May 14"),
        Article::create("Architecture of a service for collecting and classifying housing listings", "https://habr.com/ru/articles/342220", "Dec 4"),
    ],
    2015 => [
        Article::create("Migration from Symfony 2.0 to 2.6", "https://habr.com/ru/articles/258403", "May 20"),
        Article::create("SonarQube . Checking code quality", "https://habr.com/ru/articles/259149", "May 29"),
        Article::create("Nginx + Lua + Redis . Efficiently processing sessions and delivering data", "https://habr.com/ru/articles/270463", "Nov 11"),
    ]
];

$content = '';
foreach ($articles as $year => $list) {
    $content .= '<h4>' . $year . '</h4>' . PHP_EOL;
    $content .= '<div class="row">' . PHP_EOL;
    /** @var Article $article */
    foreach ($list as $index => $article) {
        $isSamePage = strpos($article->url, 'http') === false;
        $content .= sprintf(
            '<div class="col-10"><a href="%s" %s>%s</a></div>', 
            $article->url,
            $isSamePage ? '' : 'target="_blank"',
            $article->name
        );
        $content .= sprintf('<div class="col-2 text-end list-date">%s</div>', $article->date);
        if ($index < count($list) - 1) {
            $content .= '<hr class="list"/>' . PHP_EOL;
        }
    }

    $content .= '</div>' . PHP_EOL;
    $content .= '<br/>' . PHP_EOL;
}

file_put_contents(
    __DIR__ . '/../docs/articles/index.html',
    str_replace(
        [
            '{{ content }}',
            '{{ title }}',
            '{{ description }}',
            '{{ path }}',
            '{{ scripts }}',
        ],
        [
            $content,
            'Articles',
            '',
            '/articles',
            ''
        ],
        $template
    )
);

$directory = __DIR__ . '/../docs/articles';
foreach (scandir($directory) as $yearDirectory) {
    $yearDirectoryPath = $directory . '/' . $yearDirectory;
    if (!is_dir($yearDirectoryPath)) {
        continue;
    }

    if (!in_array($yearDirectory, ['2024', '2023', '2022', '2021'])) {
        continue;
    }

    foreach (scandir($yearDirectoryPath) as $articleDirectory) {
        $articleDirectoryPath = $yearDirectoryPath . '/' . $articleDirectory;
        
        $urlPath = '/articles/' . $yearDirectory . '/' . $articleDirectory . '/';

        $articleFilePath = $articleDirectoryPath . '/index.md';
        if (!is_file($articleFilePath)) {
            continue;
        }

        $file = fopen($articleFilePath, 'r');
        $name = trim(str_replace('#', '', fgets($file)));
        $description = '';
        while(strlen($description) < 100) {
            $description .= htmlspecialchars(trim(strip_tags($parser->text(fgets($file)))));
        }
        $description = substr($description, 0, 100) . '...';
        fclose($file);

        file_put_contents(
            $articleDirectoryPath . '/index.html',
            str_replace(
                [
                    '{{ content }}',
                    '{{ title }}',
                    '{{ description }}',
                    '{{ path }}',
                    '{{ scripts }}',
                ], 
                [
                    $parser->text(file_get_contents($articleFilePath)),
                    $name,
                    $description,
                    $urlPath,
                    '<link rel="stylesheet" href="/highlight.github-dark-dimmed.min.css">
<script src="/highlight.min.js"></script>
<script>hljs.highlightAll();</script>
'
                ], 
                $template
            )
        );
        
            $sitemap[] = SiteMapEntity::create(
                'https://mrsuh.com' . $urlPath, 
                date('Y-m-d', filemtime($articleFilePath))
            );
    }
}


$sitemapContent = '
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

foreach($sitemap as $entity) {
    $sitemapContent .= '  <url>' . PHP_EOL;
    $sitemapContent .= '    <loc>' . $entity->url . '</loc>' . PHP_EOL;
    $sitemapContent .= '    <lastmod>' . $entity->date . '</lastmod>' . PHP_EOL;
    $sitemapContent .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
    $sitemapContent .= '    <priority>1</priority>' . PHP_EOL;
    $sitemapContent .= '  </url>' . PHP_EOL;
}
$sitemapContent .= '</urlset>';

file_put_contents(__DIR__ . '/../docs/sitemap.xml', trim($sitemapContent));
