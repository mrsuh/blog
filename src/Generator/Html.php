<?php

namespace App\Generator;

class Html
{
    public function __construct(private string $directory, private \Twig\Environment $twig)
    {
        $this->directory = rtrim($this->directory, DIRECTORY_SEPARATOR);
    }

    public function generateToString(string $template, array $context): string
    {
        $template = $this->twig->load($template);
        
        return $template->render($context);
    }
    
    public function generate(string $filePath, string $template, array $context): void
    {
        $render = $this->generateToString($template, $context);
        
        $isHtml = str_contains($filePath, '.html');
        file_put_contents(
            $this->directory . DIRECTORY_SEPARATOR . ltrim($filePath, DIRECTORY_SEPARATOR),
            $isHtml ? self::formatHtml($render) : $render
        );
    }

    private function formatHtml(string $content): string
    {
        $tidy = new \tidy();

        $tidy->parseString($content, [
            'preserve-entities' => true,
            'hide-comments' => true,
            'tidy-mark' => false,
            'wrap' => 200,
            'wrap-attributes' => false,
            'break-before-br' => false,
            'vertical-space' => false,

            'output-xhtml' => false,
            'output-html' => true,

            'indent' => true,
            'indent-spaces' => 4,
        ]);

        $html = $tidy->html();

        return '<!DOCTYPE html>' . PHP_EOL . $html->value;
    }
}
