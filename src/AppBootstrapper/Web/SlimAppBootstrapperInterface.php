<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\AppBootstrapper\Web;

use Slim\App as SlimApp;

interface SlimAppBootstrapperInterface extends WebAppBootstrapperInterface
{
    public function getApp(): SlimApp;
}
