<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */

namespace Dotsquares\Offerbanner\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('dotsquares_offerbanner_items'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Title'
            )
			->addColumn(
                'content',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Content'
            )
			->addColumn(
                'url',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Url'
            )
			->addColumn(
                'start_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['default' => null],
                'Start_date'
            )
			->addColumn(
                'end_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['default' => null],
                'End_date'
            )
			->addColumn(
                'image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Image'
            )
			->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['default' => null],
                'Status'
            );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
