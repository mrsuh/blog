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

    /** @var Repost[] */
    public array $reposts;

    /** @var Mention */
    public array $mentions;

    public int $views = 0;
    
    public bool $pdfVersion = false;
    
    public string $abstract = '';
    
    public string $doi = '';

    public static function create(
        string $title,
        string $description,
        string $url,
        string $date,
        array  $keywords = [],
        bool   $active = true,
        array  $reposts = [],
        array  $mentions = [],
        int    $views = 0,
        bool   $pdfVersion = false,
        string $abstract = '',
        string $doi = '',
    ): self
    {
        $self = new self();
        $self->title = $title;
        $self->description = $description;
        $self->url = $url;
        $self->date = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
        $self->keywords = $keywords;
        $self->active = $active;
        $self->reposts = $reposts;
        $self->mentions = $mentions;
        $self->views = $views;
        $self->pdfVersion = $pdfVersion;
        $self->abstract = $abstract;
        $self->doi = $doi;

        return $self;
    }
}
