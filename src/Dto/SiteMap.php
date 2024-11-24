<?php

namespace App\Dto;

class SiteMap
{
    public string $url;
    public string $date;
    public string $changefreq;

    public static function create(
        string $url,
        string $date,
        string $changefreq = 'weekly',
    ): self
    {
        $self = new self();
        $self->url = $url;
        $self->date = $date;
        $self->changefreq = $changefreq;

        return $self;
    }
}
