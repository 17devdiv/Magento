<?php

/**
 * Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     2.0.1
 * @author      Dotsquares Team
 * @copyright   Copyright (c) 2021 Dotsquares. (https://www.dotsquares.com)
 *
 */
namespace Dotsquares\Marketplace\Controller\Product;
use Magento\Framework\HTTP\PhpEnvironment\Request;
/**
 * This class contains loading product image functions
 */
class Videoupload extends \Magento\Framework\App\Action\Action {
    /**
     *
     * @var $storeManager,
     * @var $resultRawFactory
     */
    protected $storeManager;
    /**
     * @var Request
     */
    protected $request;
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context            
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager            
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,Request $request) {
        parent::__construct ( $context );
        $this->_scopeConfig = $scopeConfig;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $request;
    }
    /**
     *
     * {@inheritdoc}
     *
     * @see \Magento\Framework\App\ActionInterface::execute()
     */
    public function execute() {
        $youTubeApiKey = $this->_scopeConfig->getValue ( 'catalog/product_video/youtube_api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
        $videoStatus = $this->_scopeConfig->getValue ( 'marketplace/product/product_video', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
        if (! empty ( $youTubeApiKey ) && $videoStatus =='1') {
            $post = $this->request->getPost()->toArray();
            $youtube = "http://www.youtube.com/oembed?url=" . $post ['url'] . "&format=json";
            $curl = curl_init ( $youtube );
            curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
            $return = curl_exec ( $curl );
            $url = $post ['url'];
            parse_str ( parse_url ( $url, PHP_URL_QUERY ), $my_array_of_vars );
            if(empty($my_array_of_vars ['v'])){
                $resultJson = $this->resultJsonFactory->create ();
                return $resultJson->setData ( [
                        'error' => 'Invalid URL',
                ] );
            }
            $videoId = $my_array_of_vars ['v'];
            $youtubeDesc = "https://www.googleapis.com/youtube/v3/videos?key=" . $youTubeApiKey . "&part=snippet&id=" . $videoId;
            $curl = curl_init ( $youtubeDesc );
            curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
            $returnDesc = curl_exec ( $curl );
            curl_close ( $curl );
            $resultJson = $this->resultJsonFactory->create ();
            return $resultJson->setData ( [ 
                    'success' => $return,
                    'description' => $returnDesc 
            ] );
        } else {
            ?>
<div class="name-error" style="color: red;"> <?php  ( __ ( 'Please contact administrator to update Youtube API key.' ) )?> </div>
<?php
        }
    }
}