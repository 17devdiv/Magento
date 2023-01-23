<?php

namespace Dotsquares\Productfaq\Setup;

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
            ->newTable($installer->getTable('dotsquares_productfaq_items'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['default' => null],
                'Product Id'
            )
            ->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Product Name'
            )
            ->addColumn(
                'customer_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Customer Name'
            )
            ->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Customer Email'
            )
            ->addColumn(
                'question',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Customer Question'
            )
            ->addColumn(
                'answer',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Customer Answer'
            )
            ->addColumn(
                'created_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['default' => null],
                'Created Data'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => null],
                'Status'
            );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
