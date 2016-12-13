<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\Controller;

use ActiveCollab\Bootstrap\Controller\AuthenticationAttributes\AuthenticationAttributesTrait;
use ActiveCollab\Bootstrap\Exception\CollectionNotFoundException;
use ActiveCollab\DatabaseObject\CollectionInterface;
use ActiveCollab\DatabaseObject\Entity\EntityInterface;
use ActiveCollab\DatabaseObject\Exception\ValidationException;
use ActiveCollab\DatabaseObject\PoolInterface;
use ActiveCollab\DatabaseStructure\Behaviour\CreatedByInterface;
use ActiveCollab\DatabaseStructure\Behaviour\PermissionsInterface;
use ActiveCollab\DatabaseStructure\Behaviour\ProtectedFieldsInterface;
use ActiveCollab\User\UserInterface;
use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;

/**
 * @property PoolInterface $pool
 *
 * @package ActiveCollab\Bootstrap\Controller
 */
abstract class TypeController extends Controller implements TypeControllerInterface
{
    use AuthenticationAttributesTrait;

    /**
     * @var EntityInterface
     */
    protected $active_object;

    public function __before(ServerRequestInterface $request)
    {
        $object_id = $this->getRouteParam($request, $this->getTypeIdVariable());

        if ($object_id !== null) {
            $this->active_object = $this->pool->getById($this->getTypeClassName(), $object_id);

            if (empty($this->active_object)) {
                return $this->notFound();
            }
        }

        return null;
    }

    public function index(ServerRequestInterface $request)
    {
        if ($this->shouldCheckPermissions()) {
            $authenticated_user = $this->getAuthenticatedUser($request);

            if (!$this->canList($authenticated_user)) {
                return $this->forbidden();
            }
        }

        $collection_class = $this->getCollectionClassName();

        /** @var CollectionInterface $collection */
        $collection = $this->$collection_class;

        if ($collection instanceof CollectionInterface) {
            if ($collection->isPaginated()) {
                return $collection->currentPage($this->getPageFromQueryParams($request->getQueryParams()));
            } else {
                return $collection;
            }
        } else {
            throw new CollectionNotFoundException(get_class($this), __FUNCTION__, $collection_class);
        }
    }

    public function view(ServerRequestInterface $request)
    {
        if ($this->shouldCheckPermissions()) {
            $authenticated_user = $this->getAuthenticatedUser($request);

            if (!$this->canView($this->active_object, $authenticated_user)) {
                return $this->forbidden();
            }
        }

        return $this->active_object && $this->active_object->isLoaded() ? $this->active_object : $this->notFound();
    }

    public function add(ServerRequestInterface $request)
    {
        if ($this->isReadOnly()) {
            return $this->notFound();
        }

        $authenticated_user = $this->getAuthenticatedUser($request);

        if ($this->shouldCheckPermissions() && !$this->canCreate($authenticated_user)) {
            return $this->forbidden();
        }

        $type_class = $this->getTypeClassName();
        $request_body = $request->getParsedBody();

        if (empty($request_body)) {
            $request_body = [];
        }

        if ($this->pool->isTypePolymorph($type_class)) {
            try {
                $type_class = $this->getTypeFromRequestBody($request_body);
            } catch (InvalidArgumentException $e) {
                return $this->badRequest();
            }
        }

        if ($this->requestBodyContainsProtectedFields($type_class, $request_body)) {
            return $this->badRequest();
        }

        $this->cleanUpRequestBodyForAdd($request_body, $authenticated_user);

        try {
            $result = $this->pool->produce($type_class, $request_body, false);

            if ($result instanceof CreatedByInterface && $authenticated_user && (empty($result->getCreatedById()) || !$this->canOverrideCreatedBy($authenticated_user))) {
                $result->setCreatedBy($authenticated_user); // Force created_by_id when missing and when authenticated user can't override created_by data
            }

            return $this->created($result->save());
        } catch (ValidationException $e) {
            return $e;
        }
    }

    public function edit(ServerRequestInterface $request)
    {
        if ($this->isReadOnly()) {
            return $this->notFound();
        }

        if ($this->active_object && $this->active_object->isLoaded()) {
            $authenticated_user = $this->getAuthenticatedUser($request);

            if ($this->shouldCheckPermissions() && !$this->canEdit($this->active_object, $authenticated_user)) {
                return $this->forbidden();
            }

            $request_body = $request->getParsedBody();

            if (empty($request_body)) {
                $request_body = [];
            }

            if ($this->requestBodyContainsProtectedFields($this->active_object, $request_body)) {
                return $this->badRequest();
            }

            $this->cleanUpRequestBodyForEdit($request_body, $authenticated_user);

            try {
                return $this->pool->modify($this->active_object, $request_body);
            } catch (ValidationException $e) {
                return $e;
            }
        } else {
            return $this->notFound();
        }
    }

    public function delete(ServerRequestInterface $request)
    {
        if ($this->isReadOnly()) {
            return $this->notFound();
        }

        if ($this->active_object && $this->active_object->isLoaded()) {
            $authenticated_user = $this->getAuthenticatedUser($request);

            if ($this->shouldCheckPermissions() && !$this->canDelete($this->active_object, $authenticated_user)) {
                return $this->forbidden();
            }

            $this->active_object = $this->pool->scrap($this->active_object);

            if ($this->active_object->isNew()) {
                return [
                    'single' => ['id' => $this->active_object->getId()],
                ];
            } else {
                return $this->active_object;
            }
        } else {
            return $this->notFound();
        }
    }

    // ---------------------------------------------------
    //  Utility methods
    // ---------------------------------------------------

    /**
     * Return page from request query params.
     *
     * @param  array $query_params
     * @return int
     */
    protected function getPageFromQueryParams(array $query_params)
    {
        $page = 1;

        if (isset($query_params['page'])) {
            $page = (int) $query_params['page'];
        }

        return $page < 1 ? 1 : $page;
    }

    /**
     * Get a valid type class from request body.
     *
     * @param  array  $request_body
     * @return string
     */
    protected function getTypeFromRequestBody(array &$request_body)
    {
        $type_class = $this->getTypeClassName();

        if (isset($request_body['type'])) {
            $type_class = $request_body['type'];
            unset($request_body['type']);
        }

        if (class_exists($type_class)) {
            $type_class_reflection = new ReflectionClass($type_class);

            if ($type_class_reflection->isSubclassOf($this->getTypeClassName()) && !$type_class_reflection->isAbstract()) {
                return $type_class;
            }
        }

        throw new InvalidArgumentException('Please specify a valid type');
    }

    /**
     * Return true if $request_body contains a protected field.
     *
     * @param  EntityInterface|string $object_or_object_class
     * @param  array                  $request_body
     * @return bool
     */
    private function requestBodyContainsProtectedFields($object_or_object_class, array $request_body)
    {
        if ($object_or_object_class instanceof EntityInterface) {
            $protected_fields = $object_or_object_class instanceof ProtectedFieldsInterface ? $object_or_object_class->getProtectedFields() : [];
        } elseif (is_string($object_or_object_class)) {
            $object = $this->pool->produce($object_or_object_class, [], false);

            $protected_fields = $object instanceof ProtectedFieldsInterface ? $object->getProtectedFields() : [];
        } else {
            throw new InvalidArgumentException('Valid object instance of object class expected');
        }

        foreach (array_keys($request_body) as $field) {
            if (in_array($field, $protected_fields)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Perform request data clean-up before reqeust data is being used for add action.
     *
     * @param array              $request_body
     * @param UserInterface|null $user
     */
    protected function cleanUpRequestBodyForAdd(array &$request_body, UserInterface $user = null)
    {
        $this->cleanUpRequestBody($request_body, $user);
    }

    /**
     * Perform request data clean-up before reqeust data is being used for edit action.
     *
     * @param array              $request_body
     * @param UserInterface|null $user
     */
    protected function cleanUpRequestBodyForEdit(array &$request_body, UserInterface $user = null)
    {
        $this->cleanUpRequestBody($request_body, $user);

        if ($this->pool->isTypePolymorph($this->getTypeClassName()) && array_key_exists('type', $request_body)) {
            unset($request_body['type']);
        }
    }

    /**
     * Perform request data clean-up before it is used for add or adit action.
     *
     * @param array              $request_body
     * @param UserInterface|null $user
     */
    protected function cleanUpRequestBody(array &$request_body, UserInterface $user = null)
    {
    }

    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------

    /**
     * @return bool
     */
    protected function shouldCheckPermissions()
    {
        return false;
    }

    /**
     * @param  UserInterface $user
     * @return bool
     */
    protected function canList(UserInterface $user = null)
    {
        return true;
    }

    /**
     * @param  UserInterface $user
     * @return bool
     */
    protected function canCreate(UserInterface $user = null)
    {
        return true;
    }

    /**
     * @param  EntityInterface|null $object
     * @param  UserInterface        $user
     * @return bool
     */
    protected function canView(EntityInterface $object, UserInterface $user = null)
    {
        if ($user && $user->getId()) {
            if ($object instanceof PermissionsInterface) {
                return $object->canView($user);
            }

            return true;
        }

        return false;
    }

    /**
     * @param  EntityInterface $object
     * @param  UserInterface   $user
     * @return bool
     */
    protected function canEdit(EntityInterface $object, UserInterface $user = null)
    {
        if ($user && $user->getId()) {
            if ($object instanceof PermissionsInterface) {
                return $object->canEdit($user);
            }

            return true;
        }

        return false;
    }

    /**
     * @param  EntityInterface $object
     * @param  UserInterface   $user
     * @return bool
     */
    protected function canDelete(EntityInterface $object, UserInterface $user = null)
    {
        if ($user && $user->getId()) {
            if ($object instanceof PermissionsInterface) {
                return $object->canDelete($user);
            }

            return true;
        }

        return true;
    }

    // ---------------------------------------------------
    //  Interface implementation
    // ---------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function isReadOnly(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeClassName(): string
    {
        return rtrim($this->getModelsNamespace(), '\\') . '\\' . $this->getTypeName();
    }

    /**
     * @var string
     */
    private $type_name;

    /**
     * {@inheritdoc}
     */
    public function getTypeName(): string
    {
        if (empty($this->type_name)) {
            $this->type_name = Inflector::singularize(rtrim($this->getControllerName(), 'Controller'));
        }

        return $this->type_name;
    }

    /**
     * @var string
     */
    private $type_id_variable;

    /**
     * {@inheritdoc}
     */
    public function getTypeIdVariable(): string
    {
        if (empty($this->type_id_variable)) {
            return Inflector::tableize($this->getTypeName()) . '_id';
        }

        return $this->type_id_variable;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectionClassName(): string
    {
        return rtrim($this->getCollectionsNamespace(), '\\') . '\\' . Inflector::pluralize($this->getTypeName());
    }

    /**
     * {@inheritdoc}
     */
    public function canOverrideCreatedBy(UserInterface $user = null): bool
    {
        return true;
    }

    /**
     * Return models namespace.
     *
     * @return string
     */
    abstract protected function getModelsNamespace(): string;

    /**
     * Return collections namespace.
     *
     * @return string
     */
    abstract protected function getCollectionsNamespace(): string;
}
