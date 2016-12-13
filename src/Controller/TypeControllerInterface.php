<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Controller;

use ActiveCollab\User\UserInterface;

/**
 * @package ActiveCollab\Bootstrap\Controller
 */
interface TypeControllerInterface
{
    /**
     * Return true if this contraoller is read only (allows only GET requests to be handled).
     *
     * @return bool
     */
    public function isReadOnly(): bool;

    /**
     * Return full, namespaced model collection class.
     *
     * @return string
     */
    public function getCollectionClassName(): string;

    /**
     * Return full, namespaced model type class.
     *
     * @return string
     */
    public function getTypeClassName(): string;

    /**
     * Return type name, without namespace.
     *
     * @return string
     */
    public function getTypeName(): string;

    /**
     * Return GET variable name for the type handled by this controller.
     *
     * @return string
     */
    public function getTypeIdVariable(): string;

    /**
     * Return true if $user can override created_by info when creating new objects.
     *
     * @param  UserInterface|null $user
     * @return bool
     */
    public function canOverrideCreatedBy(UserInterface $user = null): bool;
}
