<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro;

use ActiveCollab\Bootstrap\App\Metadata\UrlInterface;
use ActiveCollab\Bootstrap\Router\Retro\Sitemap\SitemapInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    private $url;
    private $sitemap;

    public function __construct(
        UrlInterface $url,
        SitemapInterface $sitemap
    )
    {
        $this->url = $url;
        $this->sitemap = $sitemap;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'link',
                function (string $routeName, array $arguments = []) {
                    return $this->url->getUrl() . $this->sitemap->urlFor($routeName, $arguments);
                },
                [
                    'is_variadic' => true,
                ]
            )
        ];
    }
}
