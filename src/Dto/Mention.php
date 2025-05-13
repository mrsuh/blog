<?php

namespace App\Dto;

class Mention
{
    public string $title;
    public string $url;
    public \DateTimeImmutable $date;
    public string $citation;

    public static function create(
        string $title,
        string $url,
        string $date,
        string $citation,
    ): self
    {
        $self = new self();
        $self->title = $title;
        $self->url = $url;
        $self->date = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
        $self->citation = $citation;

        return $self;
    }
}
