<?php

namespace App\Dto;

class Talk
{
    public string $title;
    public string $description;
    public string $url;
    public string $presentationUrl;
    public \DateTimeImmutable $date;
    public array $keywords;
    public string $posterFileName = '';

    public static function create(
        string $title,
        string $description,
        string $url,
        string $presentationUrl,
        string $date,
        array  $keywords = [],     
    ): self
    {
        $self = new self();
        $self->title = $title;
        $self->description = $description;
        $self->url = $url;
        $self->presentationUrl = $presentationUrl;
        $self->date = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
        $self->keywords = $keywords;

        return $self;
    }
}
