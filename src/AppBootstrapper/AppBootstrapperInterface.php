<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\AppBootstrapper;

use ActiveCollab\Bootstrap\AppMetadata\AppMetadataInterface;
use Interop\Container\ContainerInterface;

interface AppBootstrapperInterface
{
    public function getAppMetadata(): AppMetadataInterface;

    public function getContainer(): ContainerInterface;

    public function isBootstrapped(): bool;

    public function &bootstrap(): AppBootstrapperInterface;

    public function isRan(): bool;

    public function &run(bool $silent = false): AppBootstrapperInterface;
}
