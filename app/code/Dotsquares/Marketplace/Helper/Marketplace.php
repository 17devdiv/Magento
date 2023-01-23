<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
/**
 * This class contains manipulation functions
 */
class Marketplace extends AbstractHelper {
    
    protected $objectManager;
    
    protected $messageManager;
    
    protected $videoGalleryProcessor;
    
    protected $directoryList;
    
    protected $file;
    
    public function __construct( \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\App\Helper\Context $context,  \Dotsquares\Marketplace\Model\Product\Gallery\Video\Processor $videoGalleryProcessor,DirectoryList $directoryList, File $files) {
        $this->_objectManager = $objectManager;
        $this->messageManager= $messageManager;
        $this->videoGalleryProcessor = $videoGalleryProcessor;
        $this->directoryList = $directoryList;
        $this->files = $files;
        parent::__construct ( $context );
    }
    
    /**
     * Save downloadable information to product
     * @param object $linkModel
     * @return void
     */
    public function saveDownLoadLink($linkModel) {

        /**
         * To save download link
         */
        $linkModel->save ();
        return true;
    }
    
    public function isSellerSubscriptionEnabled($productData){
        $isSellerSubscriptionEnabled = $this->_objectManager->get ( 'Dotsquares\Marketplace\Helper\Data' )->isSellerSubscriptionEnabled ();
        $customerObj = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customerObj->getId ();
        if ($isSellerSubscriptionEnabled == 1) {
            $sellerSubscribedPlan = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Subscriptionprofiles' )->getCollection ();
            $sellerSubscribedPlan->addFieldToFilter ( 'seller_id', $customerId );
            $sellerSubscribedPlan->addFieldToFilter ( 'status', 1 );
            $sellerSubscribedPlan->addFieldtoFilter ( 'ended_at', array (
                    array (
                            'gteq' =>$this->_objectManager->get ( 'Magento\Framework\Stdlib\DateTime\DateTime' )->gmtDate (),
                    ),
                    array (
                            'ended_at',
                            'null' => ''
                    )
            ) );
            if (count ( $sellerSubscribedPlan )) {
                $maximumCount = '';
                foreach ( $sellerSubscribedPlan as $subscriptionProfile ) {
                    $maximumCount = $subscriptionProfile->getMaxProductCount ();
                    break;
                }
                $productDataTotalCount = 0;
                $sellerProduct = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->getCollection ()->addFieldToFilter ( 'seller_id', $customerId );
                $sellerIdForProducts = $sellerProduct->getAllIds ();
                $productDataTotalCount = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Bulkupload' )->getProductTotalCount ( $productData );
                $sellerProductcount = count ( $sellerIdForProducts ) + $productDataTotalCount;
                $this->subscriptionlimit ( $maximumCount, $sellerProductcount );
                return;
            } else {
                $this->messageManager->addNotice ( __ ( 'You have not subscribed any plan yet. Kindly subscribe for adding product(s).' ) );
                $this->_redirect ( 'marketplace/seller/subscriptionplans' );
                return;
            }
        }
    }
    
    public function addVideo($product,$postValue){
        $product->setStoreId(0);
        if($postValue['product_video']){
            parse_str ( parse_url ( $postValue['product_video'], PHP_URL_QUERY ), $my_array_of_vars );
            $videoId = $my_array_of_vars ['v'];
            
            // Sample video data
            $videoData = [
                    'video_id' => $videoId,
                    'video_title' => $postValue['video_title'],
                    'video_description' => $postValue['video_description'],
                    'thumbnail' => $postValue['video_thumbnailurl'],
                    'video_provider' => "youtube",
                    'video_metadata' => null,
                    'video_url' => $postValue['product_video'],
                    'media_type' => \Magento\ProductVideo\Model\Product\Attribute\Media\ExternalVideoEntryConverter::MEDIA_TYPE_CODE,
            ];
            $tmpDir = $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'Productvideo'. DIRECTORY_SEPARATOR. $videoId. DIRECTORY_SEPARATOR;
            //create folder if it is not exists
            $this->files->mkdir($tmpDir);
            $newFileName = $tmpDir . baseName($videoData['thumbnail']);
            //read file from URL and copy it to the new destination
            $this->files->read($videoData['thumbnail'], $newFileName);
            
            $videoData['file'] = $videoData['video_id'] . '_hqdefault.jpg';
            $productRepository = $this->_objectManager->create('Magento\Catalog\Api\ProductRepositoryInterface');
            $existingMediaGalleryEntries = $product->getMediaGalleryEntries();
            if(! empty($existingMediaGalleryEntries )){
            foreach ($existingMediaGalleryEntries as $key => $entry) {
                if($entry['media_type'] == 'external-video'){
                    unset($existingMediaGalleryEntries[$key]);
                }
            } 
            $product->setMediaGalleryEntries($existingMediaGalleryEntries);
            $productRepository->save($product);
            }
            // Add video to the product
            if ($product->hasGalleryAttribute()) {
                $this->videoGalleryProcessor->addVideo(
                        $product,
                        $videoData,
                        ['image', 'small_image', 'thumbnail'],
                        false,
                        true
                        );
                $product->save();
            }
        } else {
            $existingMediaGalleryEntries = $product->getMediaGalleryEntries();
            $productRepository = $this->_objectManager->create('Magento\Catalog\Api\ProductRepositoryInterface');
            if(! empty($existingMediaGalleryEntries )){
            foreach ($existingMediaGalleryEntries as $key => $entry) {
                if($entry['media_type'] == 'external-video'){
                    unset($existingMediaGalleryEntries[$key]);
                }
            }
            $product->setMediaGalleryEntries($existingMediaGalleryEntries);
            $productRepository->save($product);
            }
        }
        return $product;
    }

}