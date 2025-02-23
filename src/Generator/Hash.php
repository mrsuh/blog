<?php

namespace App\Generator;

class Hash
{
    private string $directory = '';
    private array $data = [];

    public function __construct(string $directory, private string $filePath)
    {
        $this->directory = realpath($directory);
    }

    public function load(): void
    {
        if (!is_file($this->filePath)) {
            return;
        }

        if (!is_readable($this->filePath)) {
            return;
        }

        $data = json_decode(file_get_contents($this->filePath) ?? '', true);
        if (!is_array($data)) {
            return;
        }

        $this->data = $data;
    }

    public function save(): void
    {
        file_put_contents($this->filePath, json_encode($this->data, JSON_PRETTY_PRINT));
    }

    public function isChanged(string $filePath): bool
    {
        $key = str_replace($this->directory, '', realpath($filePath));
        
        if(!isset($this->data[$key])) {
            return true;
        }
        
        $hash = '';
        if(is_file($filePath)) {
            $hash = hash_file('md5', $filePath);
        }
        
        return $this->data[$key] !== $hash;
    }

    public function set(string $filePath): void
    {
        $key = str_replace($this->directory, '', realpath($filePath));
        
        $this->data[$key] = hash_file('md5', $filePath);
    }
}
