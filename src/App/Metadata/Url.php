<?php

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\App\Metadata;

class Url implements UrlInterface
{
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}