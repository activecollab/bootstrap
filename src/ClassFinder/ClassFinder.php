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
        $dir_path_len = strlen($dir_path);
        $instance_namespace = rtrim($instance_namespace, '\\');

        if (is_dir($dir_path)) {
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir_path), RecursiveIteratorIterator::SELF_FIRST) as $file) {
                if ($file->isFile() && $file->getExtension() == 'php') {
                    $class_name = ($instance_namespace . '\\' . implode('\\', explode('/', substr($file->getPath() . '/' . $file->getBasename('.php'), $dir_path_len + 1))));

                    if (!class_exists($class_name, false)) {
                        require_once $file->getPathname();
                    }

                    $reflection_class = new ReflectionClass($class_name);

                    if (!$reflection_class->isAbstract()) {
                        $found_instance = $reflection_class->newInstanceArgs($constructor_arguments);
                        call_user_func($with_found_instance, $found_instance);
                    }
                }
            }
        }
    }
}
