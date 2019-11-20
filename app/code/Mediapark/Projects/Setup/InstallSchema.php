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

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
        $tableName = 'mediapark_projects_item';
        if (!$installer->tableExists($tableName)) {

            $table = $installer->getConnection()->newTable(
                $installer->getTable($tableName)
            )
                ->addColumn(
                    'item_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'Room ID'
                )
                ->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable => false'],
                    'Product id'
                )
                ->addColumn(
                    'item_qty',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable => false'],
                    'Quantity of products'
                )
                ->addColumn(
                    'comment',
                    Table::TYPE_TEXT,
                    512,
                    ['nullable => true'],
                    'Comment to the room '
                )
                ->addColumn(
                    'position',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable => true'],
                    'Where'
                )
                ->addColumn(
                    'params',
                    Table::TYPE_TEXT,
                    '1K',
                    ['nullable => true'],
                    'Product parameters for adding product to cart'
                )->setComment('Post Table');

            $installer->getConnection()->createTable($table);
            $installer->getConnection()->addIndex(
                $installer->getTable($tableName),
                $setup->getIdxName(
                    $installer->getTable($tableName),
                    ['comment', 'position'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['comment', 'position'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
        $tableName = 'mediapark_projects_room';
        if (!$installer->tableExists($tableName)) {

            $table = $installer->getConnection()->newTable(
                $installer->getTable($tableName)
            )
                ->addColumn(
                    'room_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'Room ID'
                )
                ->addColumn(
                    'room_name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'Room Name'
                )
                ->addColumn(
                    'room_items',
                    Table::TYPE_TEXT,
                    '1k',
                    ['nullable => true'],
                    'Item ids'
                )->setComment('Post Table');
            $installer->getConnection()->createTable($table);
            $installer->getConnection()->addIndex(
                $installer->getTable($tableName),
                $setup->getIdxName(
                    $installer->getTable($tableName),
                    ['room_name', 'room_items'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['room_name', 'room_items'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
        $tableName = 'mediapark_projects_project';
        if (!$installer->tableExists($tableName)) {

            $table = $installer->getConnection()->newTable(
                $installer->getTable($tableName)
            )
                ->addColumn(
                    'project_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'Project ID'
                )
                ->addColumn(
                    'project_name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'Project Name'
                )
                ->addColumn(
                    'project_rooms',
                    Table::TYPE_TEXT,
                    '1k',
                    ['nullable => true'],
                    'project rooms'
                )
                ->addColumn(
                    'project_comment',
                    Table::TYPE_TEXT,
                    512,
                    ['nullable => true'],
                    'project comment'
                )
                ->addColumn(
                    'project_user_id',
                    Table::TYPE_INTEGER,
                    255,
                    ['nullable => false'],
                    'User who created the project'
                )->setComment('Post Table');
            $installer->getConnection()->createTable($table);
            $installer->getConnection()->addIndex(
                $installer->getTable($tableName),
                $setup->getIdxName(
                    $installer->getTable($tableName),
                    ['project_name', 'project_rooms', 'project_comment'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['project_name', 'project_rooms', 'project_comment'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
        $installer->endSetup();
    }
}