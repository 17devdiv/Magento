<?php
namespace Dotsquares\Marketplace\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Customer\Model\GroupFactory;
use Magento\Catalog\Setup\CategorySetupFactory;

class CreateAttributes implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
	protected $groupFactory;
	private $categorySetupFactory;
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
		GroupFactory $groupFactory,
		CategorySetupFactory $categorySetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
		$this->groupFactory = $groupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $group = $this->groupFactory->create ();
        $group->setCode ( 'Marketplace Seller' )->save ();
		/** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        
		$eavSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'seller_id', ['type' => 'int','backend' => '','frontend' => '','label' => 'Seller Id',
                'input' => 'text','class' => '','source' => '','group' => 'Marketplace Details','visible' => true,'required' => false,'user_defined' => false,'default' => '',
                'searchable' => false,'filterable' => false,'comparable' => false,'visible_on_front' => false,'used_in_product_listing' => true,'unique' => false,
                'apply_to' => ''] );

        $eavSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'product_approval', ['type' => 'int','backend' => '','frontend' => '','label' => 'Product Auto Approval','input' => 'select','class' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean','group' => 'Marketplace Details','visible' => true,
                'required' => false,'user_defined' => false,'default' => '','searchable' => false,'filterable' => false,
                'comparable' => false,'visible_on_front' => false,'used_in_product_listing' => true,'apply_to' => ''] );
		
		$eavSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'is_assign_product', ['type' => 'int','backend' => '','frontend' => '','label' => 'Is Assign Product',
                    'input' => 'text','class' => '','source' => '','group' => 'Marketplace Details','visible' => true,'required' => false,'user_defined' => false,'default' => '',
                    'searchable' => false,'filterable' => false,'comparable' => false,'visible_on_front' => false,
                    'used_in_product_listing' => true,'unique' => false,'apply_to' => ''] );
		
		$eavSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'assign_product_id', [
                    'type' => 'int','backend' => '','frontend' => '','label' => 'Assign Product Id','input' => 'text',
                    'user_defined' => false,'default' => '','searchable' => false,'class' => '',
                    'source' => '','group' => 'Marketplace Details','visible' => true,'required' => false,
                    'filterable' => false,'comparable' => false,'visible_on_front' => false,
                    'used_in_product_listing' => true,'unique' => false,'apply_to' => ''] );
		
		$eavSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'config_assign_simple_id', [ 'type' => 'int',
                    'backend' => '','frontend' => '','label' => 'Assign product id [Simple Product]','input' => 'text',
                    'class' => '','source' => '','group' => 'Marketplace Details','visible' => true,'required' => false,
                    'user_defined' => false,'default' => '','searchable' => false,'filterable' => false,'comparable' => false,
                    'visible_on_front' => false,'used_in_product_listing' => true,'unique' => false,'apply_to' => ''] );
		
		$eavSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'national_shipping_amount', [
                    'type' => 'int','backend' => '','frontend' => '','label' => 'National Shipping Amount',
                    'input' => 'text','class' => '','source' => '','group' => 'Marketplace Details',
                    'visible' => true,'required' => false,'user_defined' => false,'default' => '',
                    'searchable' => false,'filterable' => false,'comparable' => false,'visible_on_front' => false,
                    'used_in_product_listing' => true,'unique' => false,'apply_to' => '' ] );
		
		$eavSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'international_shipping_amount', [
                    'type' => 'int','backend' => '','frontend' => '','label' => 'International Shipping Amount',
                    'input' => 'text','class' => '','source' => '','group' => 'Marketplace Details','visible' => true,
                    'required' => false,'user_defined' => false,'default' => '','searchable' => false,'filterable' => false,
                    'comparable' => false,'visible_on_front' => false,'used_in_product_listing' => true,'unique' => false,
                    'apply_to' => ''] );
		
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
