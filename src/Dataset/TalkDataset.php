<?php

namespace App\Dataset;

use App\Dto\Talk;

class TalkDataset
{
    /**
     * @return Talk[]
     */
    public static function get(): array
    {
        return [
            Talk::create(
                'PHP Generics',
                'PHP Generics implementation with RFC-style syntax and runtime type checking.',
                '/talks/php-generics/',
                '/talks/php-generics/php-generics-v4.pdf',
                '2022-11-25'
            ),
            Talk::create(
                'SQLite Index Visualization',
                'Explores SQLite B-Tree index structure and search operations with visualizations of navigation and storage mechanisms.',
                '/talks/sqlite-index-visualization/',
                '/talks/sqlite-index-visualization/sqlite-index-visualization-v4.pdf',
                '2025-03-12'
            ),
        ];
    }
}
