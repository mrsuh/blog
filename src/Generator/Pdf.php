<?php

namespace App\Generator;

class Pdf
{
    private string $content;
    
    public function __construct(private string $styleFilePath)
    {
        $this->content = '<style>' . file_get_contents($this->styleFilePath) . '</style>';
    }

    public function generate(string $filePath, string $directory, string $content): void
    {
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
        ]);
        $mpdf->WriteHTML($this->content);

        $mpdf->shrink_tables_to_fit = 1;
        $mpdf->use_kwt = true;
        $mpdf->showImageErrors = true;

        $mpdf->WriteHTML(self::processHtml($content, rtrim($directory, '/')));
        
        file_put_contents($filePath, $mpdf->OutputBinaryData());
    }

    private static function processHtml(string $html, string $directory): string
    {
        preg_match_all('/src="((\.\/)?[^"]+)"/', $html, $matches);
        if ($matches[1] ?? null) {

            $replaces = [];
            foreach ($matches[1] as $path) {
                $imageFilePath = $directory . '/' . str_replace('./', '', $path);
                if (!is_file($imageFilePath)) {
                    throw new \RuntimeException('File not found: ' . $imageFilePath);
                }

                $extension = pathinfo($imageFilePath, PATHINFO_EXTENSION);
                $replaces[] = sprintf('data:image/%s;base64,%s', $extension, base64_encode(file_get_contents($imageFilePath)));
            }

            $html = str_replace($matches[1], $replaces, $html);
        }
        
        $html = str_replace('<img ', '<img style="width: 100%;"', $html);
        $html = str_replace('<table ', '<table style="width: 100%"', $html);
        $html = str_replace('<td>+++</td>', '<td class="cell-fill"></td>', $html);
        $html = str_replace(
            [
                '<p>[pagebreak]</p>',
                '<p>[pagebreak-portrait]</p>',
                '<p>[pagebreak-landscape]</p>',
                '[nbsp]',
                '[br]',
            ],
            [
                '<pagebreak orientation="portrait">',
                '<pagebreak orientation="portrait">',
                '<pagebreak orientation="landscape">',
                '&nbsp;',
                '<br>',
            ],
            $html
        );
        
        $lines = explode(PHP_EOL, $html);
        foreach ($lines as $i => $line) {
            if(str_contains($line, '[pagebreak]')) {
                $lines[$i] = '<pagebreak orientation="portrait">';
            }
        }

        return implode(PHP_EOL, $lines);
    }
}
