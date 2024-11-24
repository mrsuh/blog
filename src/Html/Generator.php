<?php

namespace App\Html;

class Generator
{
    public function __construct(private string $directory, private \Twig\Environment $twig)
    {
        $this->directory = rtrim($this->directory, DIRECTORY_SEPARATOR);
    }

    public function generate(string $filePath, string $template, array $context): void
    {
        $template = $this->twig->load($template);
        file_put_contents(
            $this->directory . DIRECTORY_SEPARATOR . ltrim($filePath, DIRECTORY_SEPARATOR), 
            $template->render($context)
        );
    }
}
