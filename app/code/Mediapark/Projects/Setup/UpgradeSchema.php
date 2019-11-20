<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2019
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 */

namespace Mediapark\Projects\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
use mysql_xdevapi\Exception;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ){
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.3.5', '<')) {
            $tableName = $setup->getTable('mediapark_projects_item');
            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'room_id' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'id of parent room',
                    ],
                ];
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }

            $tableName = $setup->getTable('mediapark_projects_room');
            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'project_id' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'id of parent project',
                    ],
                ];
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.3.6', '<')) {
            $tableName = $setup->getTable('mediapark_projects_project');
            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'status' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'id of parent project',
                    ],
                    'created_at' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                        'nullable' => false,
                        'comment' => 'id of parent project',
                    ],
                ];
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.3.7', '<')) {
            $tableName = $setup->getTable('mediapark_projects_item');
            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'project_id' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'id of parent project',
                    ],
                ];
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }




        $installer->endSetup();
    }
}