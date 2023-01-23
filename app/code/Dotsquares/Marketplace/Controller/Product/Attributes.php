<?php
/**
 * Dotsquares
 *
 * @category     Dotsquares
 * @package      Dotsquares_Marketplace
 * @author      Dotsquares Team
 * @copyright   Copyright (c) 2021 Dotsquares. (http://www.dotsquares.com)
 *
 * */

namespace Dotsquares\Marketplace\Controller\Product;

/**
 * This class used to get configurable attribute block
 */
class Attributes extends \Magento\Framework\App\Action\Action
{

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

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->messageManager = $context->getMessageManager();
        parent::__construct($context);
    }

    /**
     * To create custom select attribute block
     *
     * @return void
     */
        public function execute()
    {
        /**
         * To resolving Cannot modify header information issue
         */
        ob_start();
        /**
         * Get Attribute set id and current product id
         */
        $currentProductId = $this->getRequest()->getParam('current_product_id');
        
        if ($currentProductId != "") {
            $product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($currentProductId);
            $attributeSetId = $product->getAttributeSetId();
        } else {
            $product = array();
            $attributeSetId = $this->getRequest()->getParam('custom_attribute_set_id');
        }
        $attributeGroupCollection = $this->_objectManager->get('Magento\Eav\Model\Entity\Attribute\Group')->getCollection();
        $attributeGroupCollection->addFieldToFilter('attribute_group_name', 'Custom Attribute');
        $attributeGroupCollection->addFieldToFilter('attribute_set_id', $attributeSetId);
        $attributeGroupId = '';
        foreach ($attributeGroupCollection as $attributeGroup) {
            $attributeGroupId = $attributeGroup->getId();
            break;
        }
        
        $attributeCollection = $this->_objectManager->get('Magento\Eav\Model\Entity\Attribute')->getCollection();
        $attributeCollection->setAttributeSetFilter($attributeSetId);
        $attributeCollection->setAttributeGroupFilter($attributeGroupId);
        if (count($attributeCollection) >= 1) {
            $label = __("Custom Attribute(s)");
            $html = '<label for="custom_attributes" class="label category-label"><span>' . $label . '</span></label>';
        }
        $html = '<ul>';
        foreach ($attributeCollection as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $isRequired = $attribute->getIsRequired();
            $attributeName = $attribute->getFrontendLabel();
            $attributeType = $attribute->getFrontendInput();
            $html = $html . '<li><input type="hidden" name="custom_attributes[]" value="' . $attributeCode . '" /></li>';
            switch ($attributeType) {
                case "text":
                    $html = $html . $this->textHtml($attributeCode, $isRequired, $attributeName, $product);
                    break;
                case "boolean":
                    $html = $html . $this->booleanHtml($attributeCode, $isRequired, $attributeName, $product);
                    break;
                case "select":
                case "swatch_visual":
                case "swatch_text":
                    $html = $html . $this->dropdownHtml($attributeCode, $isRequired, $attributeName, $product);
                    break;
                case "multiselect":
                    $html = $html . $this->multiSelectHtml($attributeCode, $isRequired, $attributeName, $product);
                    break;
                case "textarea":
                    $html = $html . $this->textAreaHtml($attributeCode, $isRequired, $attributeName, $product);
                    break;
                case "price":
                    $html = $html . $this->priceHtml($attributeCode, $isRequired, $attributeName, $product);
                    break;
                case "date":
                    $html = $html . $this->dateHtml($attributeCode, $isRequired, $attributeName, $product);
                    break;
                default:
                    $html = $html . '';
                    break;
            }
        }
        $html = $html . '</ul>';
		$this->getResponse()->setBody($html);
    }

    /**
     * To prepare text html
     *
     * @param string $attributeCode
     * @param boolean $isRequired
     * @param string $attributeName
     * @param object $product
     *
     * @return string
     */
    public function textHtml($attributeCode, $isRequired, $attributeName, $product)
    {
        /**
         * Assign html for text
         */
        $html = ' <li class="wide fields"><div class="field"><label for="' . $attributeCode . '"';
        /**
         * Checking for is required
         */
        if ($isRequired != 0) {
            $html = $html . 'class="required"><em>*</em>';
        } else {
            $html = $html . '>';
        }
        /**
         * Checking for attribute name
         */
        if ($attributeName == "") {
            $html = $html . __("Name Missing");
        } else {
            $html = $html . __($attributeName);
        }
        $html = $html . ' </label><div class="input-box"><input id="' . $attributeCode . '" name="product[' . $attributeCode . ']"  value=" ';
        /**
         * Checking for atttribute code
         */
        if (! empty($product[$attributeCode])) {
            $html = $html . $product[$attributeCode];
        }
        /**
         * Setting required
         */
        $html = $html . '" ';
        if ($isRequired != 0) {
            $html = $html . ' data-validate="{required:true}" ';
        }
        if ($isRequired != 0) {
            $html = $html . 'class="input-text required-entry "';
        } else {
            $html = $html . 'class="input-text"';
        }
        /**
         * Return html
         */
        $html . '  type="text" /></div></div></li>';
		$this->getResponse()->setBody($html);
    }

    /**
     * To prepare boolean html
     *
     * @param string $attributeCode
     * @param int $isRequired
     * @param string $attributeName
     * @param object $product
     *            return string
     */
    public function booleanHtml($attributeCode, $isRequired, $attributeName, $product)
    {
        
        /**
         * Getting options by attribute code
         */
        $options = $this->_objectManager->get('Magento\Catalog\Model\Product\Attribute\Repository')
            ->get($attributeCode)
            ->getOptions();
        $html = '<li class="fields"><label for="' . $attributeCode . '"';
        /**
         * Checking for is required or not
         */
        if ($isRequired != 0) {
            $html = $html . 'class="required"><em>*</em>';
        } else {
            $html = $html . '>';
        }
        /**
         * Checking attribute name
         */
        if ($attributeName == "") {
            $html = $html . __("Name Missing");
        } else {
            $html = $html . __($attributeName);
        }
        $html = $html . ' </label><div class="input-box"><select id="' . $attributeCode . '" name="product[' . $attributeCode . ']">';
        foreach ($options as $attr_option) {
            if (isset($product[$attributeCode]) && $product[$attributeCode] == $attr_option["value"]) {
                $html = $html . '<option class="attribute-boolean" selected="selected" value="' . $attr_option["value"] . '"> ' . $attr_option["label"] . '</option>';
            } else {
                $html = $html . '<option class="option-boolean" value="' . $attr_option["value"] . '"> ' . $attr_option["label"] . '</option>';
            }
        }
        /**
         * Return html
         */
        $html . '</select></div></li>';
		$this->getResponse()->setBody($html);
    }

    /**
     * To prepare dropdown attributes
     *
     * @param string $attributeCode
     * @param int $isRequired
     * @param string $attributeName
     * @param object $product
     *
     * @return string
     */
    public function dropdownHtml($attributeCode, $isRequired, $attributeName, $product)
    {
        
        /**
         * Getting options
         */
        $options = $this->_objectManager->get('Magento\Catalog\Model\Product\Attribute\Repository')
            ->get($attributeCode)
            ->getOptions();
        /**
         * Prepare html
         */
        $html = '<li class="fields"><label for="' . $attributeCode . '"';
        /**
         * Chekcing for required or not
         */
        if ($isRequired != 0) {
            $html = $html . 'class="required"><em>*</em>';
        } else {
            $html = $html . '>';
        }
        if ($attributeName == "") {
            $html = $html . __("Name Missing");
        } else {
            $html = $html . __($attributeName);
        }
        $html = $html . ' </label><div class="input-box"><select id="' . $attributeCode . '" name="product[' . $attributeCode . ']" ';
        if ($isRequired != 0) {
            $html = $html . ' data-validate="{required:true}" ';
            $html = $html . 'class="select select input-text required-entry input-text_pro"';
        } else {
            $html = $html . 'class="select select input-text input-text_pro"';
        }
        $html = $html . '>';
        $html = $html . '<option value="0">' . (__( "Select Option" )). '</option>';
        foreach ($options as $attr_option) {
            if (isset($product[$attributeCode]) && $product[$attributeCode] == $attr_option["value"]) {
                $html = $html . '<option selected="selected" value="' . $attr_option["value"] . '"> ' . $attr_option["label"] . '</option>';
            } else {
                $html = $html . '<option value="' . $attr_option["value"] . '"> ' . $attr_option["label"] . '</option>';
            }
        }
        /**
         * Return html
         */
        $html . '</select></div></li>';
		$this->getResponse()->setBody($html);
    }

    /**
     * To prepare boolean html
     *
     * @param string $attributeCode
     * @param int $isRequired
     * @param string $attributeName
     * @param object $product
     *            return string
     */
    public function multiSelectHtml($attributeCode, $isRequired, $attributeName, $product)
    {
        
        /**
         * Getting options
         */
        $options = $this->_objectManager->get('Magento\Catalog\Model\Product\Attribute\Repository')
            ->get($attributeCode)
            ->getOptions();
        /**
         * Checing for product attibutes
         */
        if (isset($product[$attributeCode])) {
            $attributeCodeArray = explode(',', $product[$attributeCode]);
        } else {
            $attributeCodeArray = array();
        }
        /**
         * Assign html
         */
        $html = '<li class="fields"><label for="' . $attributeCode . '"';
        if ($isRequired != 0) {
            $html = $html . 'class="required"><em>*</em>';
        } else {
            $html = $html . '>';
        }
        /**
         * checking for attribute name
         */
        if ($attributeName == "") {
            $html = $html . __("Name Missing");
        } else {
            $html = $html . __($attributeName);
        }
        $html = $html . ' </label>
             <div class="input-box"><select multiple="multiple" id="' . $attributeCode . '" name="product[' . $attributeCode . '][]" ';
        /**
         * checkinf for required
         */
        if ($isRequired != 0) {
            $html = $html . ' data-validate="{required:true}" ';
        }
        if ($isRequired != 0) {
            $html = $html . 'class="multiselect required-entry"';
        } else {
            $html = $html . 'class="multiselect"';
        }
        $html = $html . '>';
        foreach ($options as $attrOption) {
            if (in_array($attrOption["value"], $attributeCodeArray)) {
                $html = $html . '<option selected="selected" value="' . $attrOption["value"] . '"> ' . $attrOption["label"] . '</option>';
            } else {
                $html = $html . '<option value="' . $attrOption["value"] . '"> ' . $attrOption["label"] . '</option>';
            }
        }
        /**
         * Return html
         */
        $html . '</select></div></li>';
		$this->getResponse()->setBody($html);
    }

    /**
     * To prepare text area html
     *
     * @param string $attributeCode
     * @param int $isRequired
     * @param string $attributeName
     * @param object $product
     *
     * @return string
     */
    public function textAreaHtml($attributeCode, $isRequired, $attributeName, $product)
    {
        /**
         * Prepare html
         */
        $html = ' <li class="fields wide attr_textarea"><div class="field attr_textarea"><label for="' . $attributeCode . '"';
        /**
         * Checking for required
         */
        if ($isRequired != 0) {
            $html = $html . 'class="required"><em>*</em>';
        } else {
            $html = $html . '>';
        }
        /**
         * Checking for name
         */
        if ($attributeName == "") {
            $html = $html . __("Name Missing");
        } else {
            $html = $html . __($attributeName);
        }
        $html = $html . '</label><div class="input-box attr_textarea"><textarea id="' . $attributeCode . '" name="product[' . $attributeCode . ']" ';
        if ($isRequired != 0) {
            $html = $html . ' data-validate="{required:true}" ';
        }
        if ($isRequired != 0) {
            $html = $html . 'class="required-entry textarea attr_textarea"';
        } else {
            $html = $html . 'class="textarea"';
        }
        $html = $html . '  rows="7" cols="0" style="width: 710px;white-space: nowrap;">';
        if (! empty($product[$attributeCode])) {
            $html = $html . $product[$attributeCode];
        }
        /**
         * Return html
         */
        $html . '</textarea></div></div></li>';
		$this->getResponse()->setBody($html);
    }

    /**
     * To prepare price html
     *
     * @param string $attributeCode
     * @param int $isRequired
     * @param string $attributeName
     * @param object $product
     *
     * @return string
     */
    public function priceHtml($attributeCode, $isRequired, $attributeName, $product)
    {
        /**
         * To prepare html
         */
        $html = '<li class="fields attr-price"><label for="' . $attributeCode . '"';
        /**
         * Checking for required
         */
        if ($isRequired != 0) {
            $html = $html . 'class="required attr-price"><em>*</em>';
        } else {
            $html = $html . '>';
        }
        /**
         * Checking for attribute name
         */
        if ($attributeName == "") {
            $html = $html . __("Name Missing");
        } else {
            $html = $html . __($attributeName);
        }
        $html = $html . '  </label><div class="input-box attr-price" style="width: auto;"> <input id="' . $attributeCode . '" name="product[' . $attributeCode . ']" value="';
        if (isset($product[$attributeCode])) {
            $html = $html . $product[$attributeCode];
        }
        $html = $html . '"';
        if ($isRequired != 0) {
            $html = $html . ' data-validate="{required:true}" ';
        }
        /**
         * Checking for is required
         */
        if ($isRequired != 0) {
            $html = $html . 'class="required-entry validate-zero-or-greater input-text attr-price"';
        } else {
            $html = $html . 'class="validate-zero-or-greater input-text"';
        }
        /**
         * Assign html
         */
        $html = $html . ' type="text" ';
        /**
         * Return html
         */
        $html . '></div></li>';
		$this->getResponse()->setBody($html);
    }

    /**
     * To prepare date html
     *
     * @param string $attributeCode
     * @param boolean $isRequired
     * @param string $attributeName
     * @param object $product
     * @return string
     */
    public function dateHtml($attributeCode, $isRequired, $attributeName, $product)
    {
        /**
         * Prepare date html
         *
         * @var unknown
         */
        $html = '<li class="fields custom_input attr-date"><label for="' . $attributeCode . '">';
        if ($attributeName == "") {
            $html = $html . __("Name Missing");
        } else {
            $html = $html . __($attributeName);
        }
        /**
         * Assign attribute code
         */
        $html = $html . '</label><div class="input-box attr-date" style="width: auto;"><input name="product[' . $attributeCode . ']"  id="' . $attributeCode . '"  value="';
        if (! empty($product[$attributeCode])) {
            $html = $html . $product[$attributeCode];
        }
        $html = $html . '" class="calendar_inputField validate-date validate-date-range date-range-date-from attr-date" type="text"';
        /**
         * Return date html
         */
        $html . '/></div></li>';
		$this->getResponse()->setBody($html);
    }
}