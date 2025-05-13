<?php

namespace App\Dataset;

use App\Dto\Project;

class ProjectDataset
{
    /**
     * @return Project[]
     */
    public static function get(): array
    {
        return [
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
    }
}
