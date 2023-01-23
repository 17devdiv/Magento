<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 * */
namespace Dotsquares\Marketplace\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * This class initiates seller model
 */
class Seller extends AbstractModel {
    /**
     * Define resource model
     */
    protected function _construct() {
        $this->_init ( 'Dotsquares\Marketplace\Model\ResourceModel\Seller' );
    }
    
    /**
     * Get Image paths for the configurable products.
     * 
     * @param array $allImagesPaths
     * 
     * @return array
     */
    public function getImagesForConfigurable($allImagesPaths){
        $imagePaths = array();
        foreach ($allImagesPaths as $imagePath){
            $imagePaths[] = $imagePath;
        }
        
        return $imagePaths;
    }
}