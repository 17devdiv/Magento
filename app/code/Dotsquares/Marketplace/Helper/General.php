<?php
/**
 * Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     2.0.1
 * @author      Dotsquares Team
 * @copyright   Copyright (c) 2021 Dotsquares. (https://www.dotsquares.com)
 *
 */
namespace Dotsquares\Marketplace\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;

/**
 * This class contains manipulation functions
 */
class General extends AbstractHelper {
    /**
     * To validate image file from downloadable data
     * @param object $uploader, int $imgSize, int $validateFlag
     * @return void
     */
    public function getImageValidation($uploader, $imgSize, $validateFlag) {
        $uploaderArray = array ();
        if (! $imgSize) {
            $uploader->setFilesDispersion ( true );
            $validateFlag = 1;
        }
        $uploaderArray ['uploader'] = $uploader;
        $uploaderArray ['validate_flag'] = $validateFlag;
        return $uploaderArray;
    }

    /**
     * Assign bulk uploaded downloadable date to product
     * @param int $downloadProductId, int $store
     * @return void
     */
    public function assignDataForDownloadableProduct($downloadProductId, $store, $downloadableData) {

        if (isset ( $downloadProductId ) && isset ( $store )) {
            $this->addDownloadableProductData ( $downloadProductId, $store, $downloadableData);
        }

    }
    /**
     * Adding bulk uploaded downloadable data to product
     * @param int $downloadProductId, int $store
     * @return void
     */
    public function addDownloadableProductData($downloadProductId, $store, $downloadableData) {

        $objectGroupManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $downloadModel = $objectGroupManager->get ( 'Dotsquares\Marketplace\Model\Download');
        $downloadSample = $objectGroupManager->get ( 'Magento\Downloadable\Model\Sample');
        $downloadLink = $objectGroupManager->get ( 'Magento\Downloadable\Model\Link');

        /**
         * Initilize downloadable product sample and link files
         */
        $sampleTpath = $linkTpath = $slinkTpath = array ();

        /**
         * Getting downloadable product sample collection
         */
        $downloadableSample = $downloadSample->getCollection ()->addProductToFilter ( $downloadProductId )->addTitleToResult ( $store );
        $downloadModel->deleteDownloadableSample ( $downloadableSample );

        /**
         * Getting downloadable product link collection
         */
        $downloadableLink = $downloadLink->getCollection ()->addProductToFilter ( $downloadProductId )->addTitleToResult ( $store );
        $downloadModel->deleteDownloadableLinks ( $downloadableLink );



        try {
            /**
             * Storing Downloadable product sample data and link data
             */
            $downloadModel->saveDownLoadProductSample ( $downloadableData, $downloadProductId, $sampleTpath, $store );
            if (isset ( $downloadableData ['link'] )) {
                $downloadModel->saveDownLoadProductLink ( $downloadableData, $downloadProductId, $linkTpath, $slinkTpath, $store );
            }
        } catch ( Exception $e ) {
            $this->messageManager->addError ( __ ( 'Error.' ) );
        }
    }
}