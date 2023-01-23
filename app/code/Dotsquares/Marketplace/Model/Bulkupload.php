<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * This class initiates seller model
 */
class Bulkupload extends AbstractModel {
    /**
     * To save special price
     *
     * @param object $product
     * @param float $specialPrice
     *
     * @return object $product
     */
    public function saveSpecialData($product, $specialPrice) {
        if (! empty ( $specialPrice )) {
            $product->setSpecialPrice ( $specialPrice );
        }
        return $product;
    }
    /**
     * To st base image
     *
     * @param object $productBaseImage
     * @param string $baseImage
     * @param array $files
     * @param string $folderName
     * @param string $smallImage
     * @param string $thumbnailImage
     * @return object $productBaseImage
     */
    public function setBaseImage($productBaseImage, $baseImage, $files, $folderName, $smallImage, $thumbnailImage) {
        if (in_array ( $baseImage, $files ) && ! empty ( $baseImage )) {
            $productBaseImage->setImage ( $folderName . "/" . $baseImage );
        }
        if (in_array ( $smallImage, $files ) && ! empty ( $smallImage )) {
            $productBaseImage->setSmallImage ( $folderName . "/" . $smallImage );
        }
        if (in_array ( $thumbnailImage, $files ) && ! empty ( $thumbnailImage )) {
            $productBaseImage->setThumbnail ( $folderName . "/" . $thumbnailImage );
        }
        return $productBaseImage;
    }
    /**
     * Get attribute option values
     *
     * @param string $additionalAttributeCode
     * @param array $attributeLabelValuePair
     *
     * @return array $attributeLabelValuePair
     */
    public function getAttributeOptionValues($additionalAttributeCode, $attributeLabelValuePair) {
        /**
         * Create instance for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Getting product option values
         */
        $options = $objectManager->get ( 'Magento\Catalog\Model\Product\Attribute\Repository' )->get ( $additionalAttributeCode )->getOptions ();
        /**
         * Iterte the attribute options
         */
        foreach ( $options as $option ) {
            /**
             * Save attribute label
             */
            $optionLabel = $option->getLabel ();
            /**
             * Assign attribute label value pair
             */
            $attributeLabelValuePair [$additionalAttributeCode] [$optionLabel] = $option->getValue ();
        }
        /**
         * Return attribute label value pair
         */
        return $attributeLabelValuePair;
    }

    /**
     * Set product data
     *
     * @param object $productPrice
     * @param string $metaKeywords
     * @param string $metaDescription
     * @param float $weight
     * @param float $specialPrice
     * @param date $specialFromDate
     * @param date $specialToDate
     *
     * @return object $productPrice
     */
    public function setProductDatas($productData, $key, $productPrice) {
        $description = $specialPrice = $specialFromDate = $specialToDate = $metaKeywords = $metaDescription = '';
        $specialPrice = $productData ['special_price'] [$key];
        $specialFromDate = $productData ['special_price_from_date'] [$key];
        $specialToDate = $productData ['special_price_to_date'] [$key];
        $metaKeywords = $productData ['meta_keywords'] [$key];
        $metaDescription = $productData ['meta_description'] [$key];
        $weight = $productData ['weight'] [$key];
        $description = $productData ['description'] [$key];

        $productPrice->setDescription ( $description );
        $productPrice->setMetaKeyword ( $metaKeywords );
        $productPrice->setMetaDescription ( $metaDescription );
        $productPrice->setWeight ( $weight );
        if (! empty ( $specialPrice )) {
            $productPrice->setSpecialPrice ( $specialPrice );
        }
        $productPrice->setSpecialFromDate ( $specialFromDate );
        $productPrice->setSpecialToDate ( $specialToDate );
        return $productPrice;
    }
    /**
     * Function to get Total product count
     *
     * @param array $productData
     * @return int
     */
    public function getProductTotalCount($productData) {
        if (isset ( $productData ['sku'] )) {
            return count ( $productData ['sku'] );
        }
    }
    /**
     * Function to save status and state
     *
     * @return void
     *
     */
    public function setProductApproval($productApproval, $product, $productData, $productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $product = $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
        if ($productApproval == 1) {
            $product->setStatus ( 1 );
            $product->setProductApproval ( 1 );
        } else {
            $product->setProductApproval ( 0 );
            $product->setStatus ( 2 );
        }

        $product->save ();
    }
    /**
     * Get additional attribute values
     *
     * @param object $attr
     * @param array $additionalAttributeValue
     *            return string
     */
    public function getAdditionalAttributeVales($attr, $additionalAttributeValue) {
        if ($attr->getFrontendInput () == "multiselect") {
            $multiSelectAttributesArray = explode ( "|", $additionalAttributeValue );
            foreach ( $multiSelectAttributesArray as $multiSelectAttributesValue ) {
                $multiSelectOptionId [] = $attr->getSource ()->getOptionId ( $multiSelectAttributesValue );
            }
            return implode ( ",", $multiSelectOptionId );
        } else {
            return $attr->getSource ()->getOptionId ( $additionalAttributeValue );
        }
    }
}