<?php

namespace Dotsquares\Defaultset\Console\Command\Product;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UseDefaultOne extends Command
{
    /**
     * flag indicating if the command has been initialized yet
     *
     * @var bool
     */
    protected $initialized = false;

    /**
     * The attribute_id to use for the current command.
     *
     * @var int
     */
    protected $attributeId;

    /**
     * The entity_id values(s) to use for the command (if any).
     *
     * @var array|bool
     */
    protected $rowIds;

    /**
     * The store_id to use for the current command.
     *
     * @var int
     */
    protected $storeId;

    /**
     * The table name to use for the current command.
     *
     * @var string
     */
    protected $tableName;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var EavSetup
     */
    protected $eavSetup;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        EavSetup $eavSetup,
        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager
    ) {
        $this->connection = $resourceConnection->getConnection();
        $this->eavSetup = $eavSetup;
        $this->storeManager = $storeManager;
        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('dots:use:default-value')
            ->setDescription('Change store specific data from a product(s) of given attribute code.')
            ->addArgument(
                'attribute_code',
                InputArgument::REQUIRED,
                'Attribute Code'
            )
            ->addArgument(
                'store',
                InputArgument::REQUIRED,
                'Store code or store_id (cannot be \'admin\' or \'0\')'
            )
            ->addArgument(
                'sku',
                InputArgument::OPTIONAL,
                'Sku (omit to apply to all products)'
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input);
        $conn = $this->connection;
        $bind = [
            $conn->quoteInto('store_id = ?', $this->getStoreId()),
            $conn->quoteInto('attribute_id = ?', $this->getAttributeId())
        ];
        if ($this->getRowIds()) {
            $bind[] = $conn->quoteInto('entity_id IN (?)', $this->getRowIds());
        }
        $rows = $conn->delete($this->getTableName(), $bind);
        $output->writeln($rows . ' rows deleted.');
    }

    /**
     * Return the entity_id value(s) to use for the command (if any).
     *
     * @return array|boolean
     */
    protected function getRowIds()
    {
        if (!$this->initialized) {
            $this->errorInit(__METHOD__);
        }
        return $this->rowIds;
    }

    /**
     * Initializes some class properties.
     *
     * @param InputInterface $input
     */
    protected function init(InputInterface $input)
    {
        if (!$this->initialized) {
            $attributeCode = trim($input->getArgument('attribute_code'));
            if ($attributeCode == '') {
                throw new \RuntimeException(__('attribute_code is required.'));
            } elseif (is_numeric($attributeCode)) {
                throw new \RuntimeException(__('attribute_code cannot be numeric.'));
            }
            $attribute = $this->eavSetup->getAttribute(
                Product::ENTITY,
                $attributeCode
            );
            if (!$attribute) {
                throw new \RuntimeException(__('Invalid attribute_code "%1"', $attributeCode));
            }
            $backendType = $attribute['backend_type'];
            $allowedTypes = ['datetime', 'decimal', 'int', 'text', 'varchar'];
            if (!in_array($backendType, $allowedTypes)) {
                throw new \RuntimeException(__(
                    'backend_type "%1" is not allowed. Allowed types include: %2',
                    $backendType,
                    implode(', ', $allowedTypes)
                ));
            }
            $this->tableName = $this->connection->getTableName('catalog_product_entity_' . $backendType);
            $this->attributeId = (int) $attribute['attribute_id'];

            $store = $this->storeManager->getStore($input->getArgument('store'));
            if ($store->getCode() == 'admin') {
                throw new \RuntimeException(__('Admin Store is not allowed for this command.'));
            }
            $this->storeId = (int) $store->getId();

            $sku = trim((string)$input->getArgument('sku'));
            if ($sku != '') {
                $sql = $this->connection->select()
                    ->from($this->connection->getTableName('catalog_product_entity'), 'entity_id')
                    ->where('sku = ?', $sku);
                $rowIds = $this->connection->fetchCol($sql);
                if (!$rowIds) {
                    throw new \RuntimeException(__('Invalid Sku "%1"', $sku));
                }
                foreach ($rowIds as $k => $v) {
                    $rowIds[$k] = (int) $v;
                }
                $this->rowIds = $rowIds;
            } else {
                $this->rowIds = false;
            }

            $this->initialized = true;
        }
    }

    /**
     * Returns the attribute_id to use for the current command.
     *
     * @return int
     */
    protected function getAttributeId()
    {
        if (!$this->attributeId) {
            $this->errorInit(__METHOD__);
        }
        return $this->attributeId;
    }

    /**
     * Return the store id to use for the current command.
     *
     * @param InputInterface $input
     */
    protected function getStoreId()
    {
        if (!$this->storeId) {
            $this->errorInit(__METHOD__);
        }
        return $this->storeId;
    }

    /**
     * Return the qualified table name to use for the current command.
     *
     * @param InputInterface $input
     */
    protected function getTableName()
    {
        if (!$this->tableName) {
            $this->errorInit(__METHOD__);
        }
        return $this->tableName;
    }

    /**
     * Throws an exception.
     *
     * @param string $methodName
     * @throws \LogicException
     */
    protected function errorInit($methodName)
    {
        throw new \LogicException(
            __('Command has not been intialized. Call UseDefaultValue::init() before calling ' . $methodName)
        );
    }
}
