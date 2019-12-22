<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\SitemapPathResolver;

class SitemapPathResolver implements SitemapPathResolverInterface
{
    private $sitemap_path;

    public function __construct(string $sitemap_path)
    {
        $this->sitemap_path = $sitemap_path;
    }

    public function getSitemapPath(): string
    {
        return $this->sitemap_path;
    }
}
