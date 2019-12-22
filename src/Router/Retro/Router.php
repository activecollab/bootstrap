<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro;

use ActiveCollab\Bootstrap\Router\Retro\Nodes\Directory;
use ActiveCollab\Bootstrap\Router\Retro\Nodes\DirectoryInterface;
use ActiveCollab\Bootstrap\Router\Retro\Nodes\File;
use DirectoryIterator;
use RuntimeException;

class Router implements RouterInterface
{
    public function scan(string $routing_root): DirectoryInterface
    {
        $routing_root = rtrim($routing_root, '/');

        if (!is_dir($routing_root)) {
            throw new RuntimeException(sprintf('Path "%s" is not a directory.', $routing_root));
        }

        return $this->scanDir($routing_root, '');
    }

    private function scanDir(string $routing_root, string $dir_path): DirectoryInterface
    {
        $result = new Directory($routing_root, $dir_path);

        foreach (new DirectoryIterator($routing_root . '/' . $dir_path) as $entity) {
            if ($entity->isDot() || $entity->isLink()) {
                continue;
            }

            $node_path = $this->getNodePath($routing_root, $entity->getPathname());

            if ($entity->isFile()) {
                $result->addFiles(new File($routing_root, $node_path));
            } elseif ($entity->isDir()) {
                $result->addSubdirectory($this->scanDir($routing_root, $node_path));
            }
        }

        return $result;
    }

    private function getNodePath(string $routing_root, string $node_path): string
    {
        return mb_substr($node_path, mb_strlen($routing_root) + 1);
    }
}
