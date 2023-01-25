<?php

namespace Magenest\Salesforce\Controller\Adminhtml\System\Config\Getauth;

use Magento\Backend\App\Action;
use Magenest\Salesforce\Model\Connector;
use Magenest\Salesforce\Model\Sync\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class GetAuth
 * @package Magenest\Salesforce\Controller\Adminhtml\System\Config\Getauth
 */
class GetAuth extends Action
{
    /** @var string  */
    const ERROR_CONNECT_TO_SALESFORCECRM = 'INVALID_PASSWORD';

    /**
     * @var \Magenest\Salesforce\Model\Connector
     */
    protected $_connector;

    /**
     * @var Product
     */
    protected $_syncProduct;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /** @var \Magento\Framework\Serialize\Serializer\Json  */
    protected $_json;

    /** @var \Magenest\Salesforce\Logger\Logger  */
    protected $_logger;

    /**
     * GetAuth constructor.
     *
     * @param Action\Context $context
     * @param Connector $connector
     * @param ScopeConfigInterface $scopeConfig
     * @param Product $syncProduct
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magenest\Salesforce\Logger\Logger $logger
     */
    public function __construct(
        Action\Context $context,
        Connector $connector,
        ScopeConfigInterface $scopeConfig,
        Product $syncProduct,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magenest\Salesforce\Logger\Logger $logger
    )
    {
        parent::__construct($context);
        $this->_cacheTypeList     = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_connector         = $connector;
        $this->_syncProduct       = $syncProduct;
        $this->scopeConfig        = $scopeConfig;
        $this->_json = $json;
        $this->_logger = $logger;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            return $this->_redirect('noroute');
        }
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            if (empty($data['username']) || empty($data['password']) || empty($data['client_id']) || empty($data['client_secret']) || empty($data['security_token'])) {
                $result['error']   = 1;
                $result['message'] = __("Please enter all information");
                $this->getResponse()->setBody($this->_json->serialize($result));
                return;
            }
            try {
                $response = $this->_connector->getAccessToken($data, true);
                if (!empty($response['error'])) {
                    $result['error']   = 1;
                    $result['message'] = $response['error_description'];
                    $this->_logger->info($response['error_description']);
                    $this->getResponse()->setBody($this->_json->serialize($result));
                    return;
                } else {
                    $result          = $response;
                    $result['error'] = 0;
                    try {
                        $this->_syncProduct->setCredential($data);
                        $this->_syncProduct->syncShippingProduct();
                        $this->_syncProduct->syncTaxProduct();
                    } catch (\Exception $e) {
                        $result['error']   = 1;
                        $result['message'] = $e->getMessage();
                        $this->_logger->info($e->getMessage());
                    }
                    $this->getResponse()->setBody($this->_json->serialize($result));
                    $this->cleanConfig();
                    return;
                }
            } catch (\Exception $e) {
                $result['error']   = 1;
                $result['message'] = $e->getMessage();
                $this->_logger->info($e->getMessage());
                $this->getResponse()->setBody($this->_json->serialize($result));
                return;
            }
        }
    }

    /**
     * Clear config after get access token success
     */
    private function cleanConfig()
    {
        $types = array('config', 'full_page');
        foreach ($types as $type) {
            $this->_cacheTypeList->invalidate($type);
        }
        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }
}
