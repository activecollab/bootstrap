<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\ClassFinder;

use ActiveCollab\Bootstrap\ClassFinder\ClassDir\ClassDir;
use ActiveCollab\Bootstrap\ClassFinder\ClassDir\ClassDirInterface;
use ReflectionClass;

/**
 * @package ActiveCollab\Bootstrap\ClassFinder
 */
interface ClassFinderInterface
{
    /**
     * Recurisively scan multiple dirs for instances.
     *
     * @param  ClassDir[] $class_dirs
     * @param  callable   $with_found_instance
     * @param  array|null $constructor_arguments
     * @return array
     */
    public function scanDirsForInstances(array $class_dirs, callable $with_found_instance, array $constructor_arguments = []);

    /**
     * Scan a dir for instances.
     *
     * @param ClassDirInterface $class_dir
     * @param callable          $with_found_instance
     * @param array|null        $constructor_arguments
     */
    public function scanDirForInstances(ClassDirInterface $class_dir, callable $with_found_instance, array $constructor_arguments = []);

    /**
     * Scan directory for classes.
     *
     * @param  ClassDirInterface $class_dir
     * @param  bool              $skip_abstract
     * @param  bool              $skip_non_descendant
     * @return ReflectionClass[]
     */
    public function scanDirForClasses(ClassDirInterface $class_dir, $skip_abstract = false, $skip_non_descendant = false): array;
}
