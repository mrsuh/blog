<?php

namespace App\Dto;

class Project
{
    public string $title;
    public string $description;
    public string $url;
    public array $keywords;
    public string $posterFileName = 'poster.png';

    public static function create(
        string $title,
        string $description,
        string $url,
        array  $keywords = [],
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
