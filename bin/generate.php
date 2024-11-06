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

        $data['element']['attributes']['class'] = 'img-fluid mx-auto d-block';

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
}

$parser = new MyParserdown();

$parser->setBreaksEnabled(true);

$template = file_get_contents(__DIR__ . '/../src/template.html');

file_put_contents(
    __DIR__ . '/../docs/index.html',
    str_replace('{{ content }}', $parser->text(file_get_contents(__DIR__ . '/../src/index.md')), $template)
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

$articles = [
    2023 => [
        Article::create("How I Wrote PHP Skeleton For Bison", "https://devm.io/php/php-skeleton-bison-generics", "Sep 15"),
        Article::create("JSON parser with PHP and Bison", "https://dev.to/mrsuh/json-parser-with-php-and-bison-385n", "Apr 3"),
        Article::create("Nginx parser with PHP and Bison", "https://dev.to/mrsuh/nginx-parser-with-php-and-bison-1k5", "Mar 27"),
        Article::create("AST parser with PHP and Bison", "https://dev.to/mrsuh/ast-parser-with-php-and-bison-l5h", "Mar 19"),
        Article::create("PHP Skeleton for Bison", "https://dev.to/mrsuh/php-skeleton-for-bison-po2", "Mar 13"),
        Article::create("Few steps to make your docker image smaller", "https://dev.to/mrsuh/few-steps-to-make-your-docker-image-smaller-4pc6", "Feb 20"),
    ],
    2022 => [
        Article::create("PHP generics", "https://phprussia.ru/moscow/2022/abstracts/9165", "Nov 25"),
        Article::create("How PHP engine builds AST", "https://dev.to/mrsuh/how-php-engine-builds-ast-1nc4", "Sep 5"),
        Article::create("Parsing with PHP, Bison and re2c", "https://dev.to/mrsuh/parse-files-with-php-bison-and-re2c-1i6p", "Aug 26"),
        Article::create("Telegram bot that monitors currency availability in Tinkoff ATMs", "https://vc.ru/u/585016-anton-suhachev/393167-telegram-bot-kotoryi-sledit-za-valyutoi-v-bankomatah-tinkoff", "Apr 02"),
        Article::create("Comparing PHP Collections", "https://dev.to/mrsuh/comparing-php-collections-5cca", "Mar 22"),
        Article::create("Generics implementation approaches", "https://dev.to/mrsuh/generics-implementation-approaches-3bf0", "Feb 8"),
    ],
    2021 => [
        Article::create("PHP Generics . Right here . Right now", "https://habr.com/ru/articles/577750", "Sep 14"),
    ],
    2020 => [
        Article::create("RC - boat with ESP8266 NodeMCU", "https://habr.com/ru/articles/513482", "Nov 3"),
        Article::create("RC - car with ESP8266 NodeMCU and LEGO", "https://vc.ru/dev/160142-rc-mashinka-iz-esp8266-nodemcu-i-lego", "Sep 21"),
        Article::create("Looking for the most interesting articles on the site", "https://vc.ru/dev/159230-ishem-samye-interesnye-stati-v-razdelah-na-saitah-vcru-tjournalru-i-dtfru", "Sep 17"),
        Article::create("How I migrated my hobby project to k8s", "https://habr.com/ru/articles/484528", "Jan 21"),
    ],

    2019 => [
        Article::create("Comparing PHP FPM, PHP PPM, Nginx Unit, ReactPHP, and RoadRunner", "https://habr.com/ru/articles/431818", "Jan 14"),
    ],
    2018 => [
        Article::create("Mafia with Go, Vanila JS and WebSockets", "https://habr.com/ru/articles/423821", "Oct 5"),
    ],
    2017 => [
        Article::create("Architecture of a service for collecting and classifying housing listings", "https://habr.com/ru/articles/342220", "Dec 4"),
        Article::create("Classifying listings from social networks: In search of the best solution", "https://habr.com/ru/articles/328282", "May 14"),
        Article::create("Continuous delivery with Travis CI and Ansible", "https://habr.com/ru/articles/325438", "Apr 3"),

    ],
    2015 => [
        Article::create("Nginx + Lua + Redis . Efficiently processing sessions and delivering data", "https://habr.com/ru/articles/270463", "Nov 11"),
        Article::create("SonarQube . Checking code quality", "https://habr.com/ru/articles/259149", "May 29"),
        Article::create("Migration from Symfony 2.0 to 2.6", "https://habr.com/ru/articles/258403", "May 20"),
    ]
];

$content = '';
foreach ($articles as $year => $list) {
    $content .= '<h4>' . $year . '</h4>' . PHP_EOL;
    $content .= '<div class="row">' . PHP_EOL;
    /** @var Article $article */
    foreach ($list as $index => $article) {
        $content .= sprintf('<div class="col-10"><a href="%s" target="_blank">%s</a></div>', $article->url, $article->name);
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
    str_replace('{{ content }}', $content, $template)
);

$directory = __DIR__ . '/../docs/articles';
foreach (scandir($directory) as $yearDirectory) {
    $yearDirectoryPath = $directory . '/' . $yearDirectory;
    if (!is_dir($yearDirectoryPath)) {
        continue;
    }

    if ($yearDirectory !== '2024') {
        continue;
    }

    foreach (scandir($yearDirectoryPath) as $articleDirectory) {
        $articleDirectoryPath = $yearDirectoryPath . '/' . $articleDirectory;

        $articleFilePath = $articleDirectoryPath . '/index.md';
        if (!is_file($articleFilePath)) {
            continue;
        }

        file_put_contents(
            $articleDirectoryPath . '/index.html',
            str_replace('{{ content }}', $parser->text(file_get_contents($articleFilePath)), $template)
        );
    }
}
