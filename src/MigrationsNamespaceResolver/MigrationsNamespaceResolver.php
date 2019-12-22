<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\MigrationsNamespaceResolver;

class MigrationsNamespaceResolver implements MigrationsNamespaceResolverInterface
{
    private $migrations_namespace;

    public function __construct(string $migrations_namespace)
    {
        $this->migrations_namespace = $migrations_namespace;
    }

    public function getMigrationsNamespace(): string
    {
        return $this->migrations_namespace;
    }
}
