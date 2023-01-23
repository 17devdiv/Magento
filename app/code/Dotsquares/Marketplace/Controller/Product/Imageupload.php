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

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * This class contains loading product image functions
 */
class Imageupload extends \Magento\Framework\App\Action\Action {
    /**
     *
     * @var $storeManager,
     * @var $resultRawFactory
     */
  
    protected $storeManager;
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager
	) {
        parent::__construct ( $context );
        $this->storeManager = $storeManager;
    }
   /**
    * 
    * {@inheritDoc}
    * @see \Magento\Framework\App\ActionInterface::execute()
    */
    public function execute() {
		$htmlResult = '';
		$mediaDir = $this->_objectManager->get ( 'Magento\Framework\Filesystem' )->getDirectoryRead ( DirectoryList::MEDIA )->getAbsolutePath();;
		$mediapath = $this->_mediaBaseDirectory = rtrim($mediaDir, '/');
		$images = $this->getRequest()->getFiles('product_images');
		if(isset($images)){
			
		  $i = 0;
		  foreach ($images as $files) {
			  
			if (isset($files['tmp_name']) && strlen($files['tmp_name']) > 0) {
			  try {
				 
				$uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' => 'product_images['.$i. ']']);
				
				$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
				$uploader->setAllowRenameFiles(true);
				$path = $mediapath . '/tmp/catalog/product/';
				$data['images'] = $images[$i]['name'];
				$result = $uploader->save($path);
				
				$result ['url'] = $this->_objectManager->get ( 'Magento\Catalog\Model\Product\Media\Config' )->getTmpMediaUrl ( $result ['file'] );
					$fileName = $result ['file'];

				$absPath = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . 'tmp/catalog/product/' . $fileName;

				$htmlResult = $htmlResult . '<span><span class="image_close">x</span><span class="base_image_container"><input class="base_image" type="radio" name="base_image" value="' . $fileName . '"></span><img src="' . $absPath . '" alt="' . $absPath . '" height="200" width="200"><input class="hidden_uploaded_image_path" type="hidden" name="images_path[]" value="' . $fileName . '" /></span>';
				
			  }catch (\Exception $e) {
				$this->messageManager->addError(__($e->getMessage()));
			  }
			}
			$i++;
		  }
		}
		$this->getResponse()->setBody($htmlResult);
		
    }
}