<?php

namespace Dotsquares\Defaultset\Console\Command\Product;


use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UseDefaultValueAll extends Command
{
    /**
     * flag indicating if the command has been initialized yet
     *
     * @var bool
     */
    protected $initialized = false;

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
            ->setName('dots:use:default-value-all')
            ->setDescription('Change store specific data from a product of all attribute code.')
            ->addArgument(
                'store',
                InputArgument::REQUIRED,
                'Store code or store_id (cannot be \'admin\' or \'0\')'
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
        ];
        if ($this->getRowIds()) {
            print_r($bind[] = $conn->quoteInto('entity_id IN (?)', $this->getRowIds()));
        }


        $rows = $conn->delete('catalog_product_entity_text', $bind);
        $rows1 = $conn->delete('catalog_product_entity_datetime', $bind);
        $rows2 = $conn->delete('catalog_product_entity_decimal', $bind);
        $rows3 = $conn->delete('catalog_product_entity_int', $bind);
        $rows4 = $conn->delete('catalog_product_entity_varchar', $bind);
        $output->writeln("Successfully");
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
            $store = $this->storeManager->getStore($input->getArgument('store'));
            if ($store->getCode() == 'admin') {
                throw new \RuntimeException(__('Admin Store is not allowed for this command.'));
            }
            $this->storeId = (int) $store->getId();
            $this->initialized = true;
        }
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
     * Throws an exception.
     *
     * @param string $methodName
     * @throws \LogicException
     */
    protected function errorInit($methodName)
    {
        throw new \LogicException(
            __('Command has not been intialized. Call UseDefaultValue::init() before calling ' . $methodName)
        );;
    }
}
