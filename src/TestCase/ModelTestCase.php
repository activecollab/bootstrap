<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\TestCase;

use ActiveCollab\Bootstrap\ClassFinder\ClassFinder;
use ActiveCollab\DatabaseObject\Pool;
use ActiveCollab\DatabaseObject\PoolInterface;
use ActiveCollab\DatabaseStructure\StructureInterface;
use RuntimeException;

/**
 * @property PoolInterface $pool
 * @property StructureInterface $structure
 *
 * @package ActiveCollab\Id\Test
 */
abstract class ModelTestCase extends DatabaseTestCase
{
    /**
     * Set up test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->addToContainer('pool', function ($c) {
            $pool = new Pool($c['connection']);
            $pool->setContainer($this->getContainer());

            $types_file = "{$c['app_root']}/app/src/Model/types.php";

            if (is_file($types_file)) {
                $pool->registerType(...require $types_file);
            }

            $collections_dir = "{$c['app_root']}/app/src/Model/Collection";

            if (is_dir($collections_dir)) {
                foreach ((new ClassFinder())->scanDirForClasses($collections_dir, $this->getModelNamespace() . '\\Collection') as $class_name) {
                    $c[ltrim($class_name, '\\')] = function ($c) use ($class_name) {
                        return new $class_name($c['connection'], $c['pool'], $c['logger']);
                    };
                }
            }

            $produces_dir = "{$c['app_root']}/app/src/Model/Producer";

            if (is_dir($produces_dir)) {
                foreach (new \DirectoryIterator("{$c['app_root']}/app/src/Model/Producer") as $file) {
                    if ($file->isFile() && $file->getExtension() == 'php') {
                        $producer_class_name = $this->getModelNamespace() . '\\Producer\\' . $file->getBasename('.php');
                        $model_class_name = $this->getModelNamespace() . '\\' . $file->getBasename('.php');

                        if ((new \ReflectionClass($producer_class_name))->isAbstract()) {
                            continue;
                        }

                        $pool->registerProducerByClass($model_class_name, $producer_class_name);
                    }
                }
            }

            /** @var \mysqli $link */
            $link = $c['link'];

            $structure_sql_file_path = "{$c['app_root']}/app/src/Model/structure.sql";

            if (is_file($structure_sql_file_path)) {
                $link->multi_query(str_replace(["delimiter //\n", "//\ndelimiter ;"], ['', ''], file_get_contents($structure_sql_file_path)));

                do {
                    $link->next_result();
                } while ($link->more_results());
            }

            return $pool;
        });

        $this->addToContainer('structure', function ($c) {
            $structure_class_name = $this->getModelNamespace() . '\\Structure';

            if (class_exists($structure_class_name)) {
                return new $structure_class_name();
            } else {
                throw new RuntimeException("Class '$structure_class_name' is not defined");
            }
        });
    }

    /**
     * @return string
     */
    abstract protected function getModelNamespace();
}
