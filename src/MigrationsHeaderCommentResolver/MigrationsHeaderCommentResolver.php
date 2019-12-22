<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\MigrationsHeaderCommentResolver;

class MigrationsHeaderCommentResolver implements MigrationsHeaderCommentResolverInterface
{
    private $migrations_header_comment;

    public function __construct(string $migrations_header_comment)
    {
        $this->migrations_header_comment = $migrations_header_comment;
    }

    public function getMigrationsHeaderComment(): string
    {
        return $this->migrations_header_comment;
    }
}
