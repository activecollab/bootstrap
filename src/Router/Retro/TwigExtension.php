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
use ActiveCollab\ContainerAccess\ContainerAccessInterface;
use ActiveCollab\ContainerAccess\ContainerAccessInterface\Implementation as ContainerAccessImplementation;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension implements ContainerAccessInterface
{
    use ContainerAccessImplementation;

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'link',
                function (string $routeName, array $arguments = []) {
                    return sprintf(
                        '%s%s',
                        $this->container->get(UrlInterface::class)->getUrl(),
                        $this->container->get(SitemapInterface::class)->urlFor($routeName, $arguments)
                    );
                },
                [
                    'is_variadic' => true,
                ]
            ),

            new TwigFunction(
                'asset',
                function (string $assetPath) {
                    return sprintf(
                        '%s/assets/%s',
                        $this->container->get(UrlInterface::class)->getUrl(),
                        $assetPath
                    );
                }
            ),

            new TwigFunction(
                'application_script',
                function () {
                    return sprintf(
                        '%s/assets/application.js',
                        $this->container->get(UrlInterface::class)->getUrl(),
                    );
                }
            ),

            new TwigFunction(
                'application_style',
                function () {
                    return sprintf(
                        '%s/assets/main.css',
                        $this->container->get(UrlInterface::class)->getUrl(),
                    );
                }
            ),
        ];
    }
}
