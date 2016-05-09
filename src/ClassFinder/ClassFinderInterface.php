<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\ClassFinder;

/**
 * @package ActiveCollab\Bootstrap\ClassFinder
 */
interface ClassFinderInterface
{
    /**
     * Recurisively scan multiple dirs for instances.
     *
     * @param array      $dirs
     * @param callable   $with_found_instance
     * @param array|null $constructor_arguments
     */
    public function scanDirsForInstances(array $dirs, callable $with_found_instance, array $constructor_arguments = []);

    /**
     * Scan a dir for instances.
     *
     * @param string     $dir_path
     * @param string     $instance_namespace
     * @param callable   $with_found_instance
     * @param array|null $constructor_arguments
     */
    public function scanDirForInstances($dir_path, $instance_namespace, callable $with_found_instance, array $constructor_arguments = []);

    /**
     * Scan directory for classes.
     *
     * @param  string $dir_path
     * @param  string $instance_namespace
     * @param  bool   $skip_abstract
     * @return array
     */
    public function scanDirForClasses($dir_path, $instance_namespace, $skip_abstract = false);
}
