<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\ClassFinder;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

/**
 * @package ActiveCollab\Bootstrap\ClassFinder
 */
class ClassFinder implements ClassFinderInterface
{
    /**
     * {@inheritdoc}
     */
    public function scanDirs(array $dirs, callable $with_found_instance, array $constructor_arguments = [])
    {
        foreach ($dirs as $dir_path => $instance_namespace) {
            $this->scanDir($dir_path, $instance_namespace, $with_found_instance, $constructor_arguments);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function scanDir($dir_path, $instance_namespace, callable $with_found_instance, array $constructor_arguments = [])
    {
        foreach ($this->scanDirForClasses($dir_path, $instance_namespace) as $class_path => $class_name) {
            if (!class_exists($class_name, false)) {
                require_once $class_path;
            }

            $reflection_class = new ReflectionClass($class_name);

            if (!$reflection_class->isAbstract()) {
                $found_instance = $reflection_class->newInstanceArgs($constructor_arguments);
                call_user_func($with_found_instance, $found_instance);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function scanDirForClasses($dir_path, $instance_namespace)
    {
        $result = [];

        $dir_path = rtrim($dir_path, '/');

        if (is_dir($dir_path)) {
            $dir_path_len = strlen($dir_path);
            $instance_namespace = rtrim($instance_namespace, '\\');

            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir_path), RecursiveIteratorIterator::SELF_FIRST) as $file) {
                if ($file->isFile() && $file->getExtension() == 'php') {
                    $result[$file->getPathname()] = $instance_namespace . '\\' . implode('\\', explode('/', substr($file->getPath() . '/' . $file->getBasename('.php'), $dir_path_len + 1)));
                }
            }
        }

        return $result;
    }
}
