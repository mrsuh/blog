<?php

namespace App\Dto;

class Rss
{
    public string $url;
    public \DateTimeInterface $date;
    public string $title;
    public string $description;

    public static function create(
        string $url,
        \DateTimeInterface $date,
        string $title,
        string $description,
    ): self
    {
        $self = new self();
        $self->url = $url;
        $self->date = $date;
        $self->title = $title;
        $self->description = $description;

        return $self;
    }
}
