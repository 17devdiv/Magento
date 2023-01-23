<?php

/**
 * Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     2.0.1
 * @author      Dotsquares Team
 * @copyright   Copyright (c) 2021 Dotsquares. (https://www.dotsquares.com)
 *
 */
namespace Dotsquares\Marketplace\Controller\Configurable;

/**
 * This class used to get configurable attribute block
 */
class Attributes extends \Magento\Framework\App\Action\Action {
    
    /**
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    
    /**
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory) {
        $this->resultPageFactory = $resultPageFactory;
        $this->messageManager = $context->getMessageManager();
        parent::__construct ( $context );
    }
    
    /**
     * To create configurable select attribute block
     *
     * @return void
     */
    public function execute() {
        /**
         * Get Attribute set id and current product id
         */
        $attributeSetId = $this->getRequest ()->getParam ( 'attribute_set_id' );
        $currentProductId = $this->getRequest ()->getParam ( 'current_product_id' );
        
        /**
         * Prepare current product attributes
         */
        $selectedAttributes = array ();
        if ($currentProductId) {
            $product = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $currentProductId );
            if ($product->getTypeId () == 'configurable') {
                $configurableAttributes = $product->getTypeInstance ( true )->getConfigurableAttributesAsArray ( $product );
                foreach ( $configurableAttributes as $configurableAttribute ) {
                    $selectedAttributes [] = $configurableAttribute ['attribute_id'];
                }
            }
        }
        
        /**
         * Product Types
         */
        $types = [ 
                \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
                \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
                \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE 
        ];
        
        $attributes = $this->_objectManager->get ( 'Magento\ConfigurableProduct\Model\ConfigurableAttributeHandler' )->getApplicableAttributes ();
        $attributes->addFieldToFilter ( 'entity_type_id', $attributeSetId );
        
        /**
         * Prepare select attribute content
         */
        $htmlResult = $htmlResult . '<ul>';
        foreach ( $attributes as $attribute ) {
            /**
             * Checking for configurable attribute or not
             */
            if (! $attribute->getApplyTo () || count ( array_diff ( $types, $attribute->getApplyTo () ) ) === 0) {
                $checked = '';
                if (count ( $selectedAttributes ) > 0 && in_array ( $attribute->getAttributeId (), $selectedAttributes )) {
                    $checked = 'checked onclick="return false;" onkeydown="return false;"';
                }
                $htmlResult = $htmlResult . '<li><input id="' . $attribute->getAttributeCode () . '" ' . $checked . ' name="attributes[' . $attribute->getAttributeCode () . ']" value="' . $attribute->getFrontendLabel () . '"
  title="' . $attribute->getFrontendLabel () . '" 
  class="attribute-checkbox validate-one-required-by-name" type="checkbox">';
  $htmlResult = $htmlResult . '<input type="hidden" name="attribute_ids[' . $attribute->getAttributeCode () . ']" value="' . $attribute->getAttributeId () . '" />';
                
  $htmlResult = $htmlResult . '<label for="' . $attribute->getAttributeCode () . '">' . $attribute->getFrontendLabel () . '</label></li>';
            }
        }
        $htmlResult = $htmlResult . '</ul>';
        return $htmlResult;
    }
}