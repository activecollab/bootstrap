<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\TestCase;

use ActiveCollab\DatabaseObject\Pool;
use ActiveCollab\DatabaseObject\PoolInterface;
use ActiveCollab\DatabaseStructure\StructureInterface;

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
            $pool->registerType(...require "{$c['app_root']}/app/src/Model/types.php");

            foreach (new \DirectoryIterator("{$c['app_root']}/app/src/Model/Producer") as $file) {
                if ($file->isFile() && $file->getExtension() == 'php') {
                    $producer_class_name = $this->getModelNamespace() . '\\' . $file->getBasename('.php');
                    $model_class_name = $this->getModelNamespace() . '\\' . $file->getBasename('.php');

                    if ((new \ReflectionClass($producer_class_name))->isAbstract()) {
                        continue;
                    }

                    $pool->registerProducerByClass($model_class_name, $producer_class_name);
                }
            }

            return $pool;
        });

        $this->addToContainer('structure', function ($c) {
            /** @var \mysqli $link */
            $link = $c['link'];

            $structure = $this->getModelStructure();

            $structure_sql_file_path = "{$c['app_root']}/app/src/Model/structure.sql";

            if (is_file($structure_sql_file_path)) {
                $link->multi_query(str_replace(["delimiter //\n", "//\ndelimiter ;"], ['', ''], file_get_contents($structure_sql_file_path)));

                do {
                    $link->next_result();
                } while ($link->more_results());
            }

            return $structure;
        });
    }

    /**
     * @return string
     */
    abstract protected function getModelNamespace();

    /**
     * @return StructureInterface
     */
    abstract protected function getModelStructure();
}
