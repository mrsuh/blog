<?php

namespace App\Dto;

class Repost
{
    public const YCOMBINATOR = 'ycombinator';
    public const REDDIT = 'reddit';
    public const LINKEDIN = 'linkedin';
    public const X = 'x';
    
    public string $url;

    public static function create(
        string $url,
    ): self
    {
        $self = new self();
        $self->url = $url;

        return $self;
    }
}
