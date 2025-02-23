<?php

namespace App\Dto;

class Article
{
    public string $title;
    public string $description;
    public string $url;
    public \DateTimeImmutable $date;
    public array $keywords;
    public bool $active;

    public static function create(
        string $title,
        string $description,
        string $url,
        string $date,
        array  $keywords = [],
        bool   $active = true,
    ): self
    {
        $self = new self();
        $self->title = $title;
        $self->description = $description;
        $self->url = $url;
        $self->date = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
        $self->keywords = $keywords;
        $self->active = $active;

        return $self;
    }
}
