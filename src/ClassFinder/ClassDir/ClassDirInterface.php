<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\ClassFinder\ClassDir;

interface ClassDirInterface
{
    public function getPath(): string;

    public function getNamespace(): string;

    public function getInstanceClass(): string;
}
