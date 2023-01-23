<?php

namespace Dotsquares\Productfaq\Controller\Index; 

use Magento\Framework\Controller\ResultFactory; 

class Index extends \Magento\Framework\App\Action\Action
{
    protected $resource;
    protected $storeManager;
    protected $transportBuilder;
    protected $inlineTranslation;
    protected $date;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\ResourceConnection  $resource,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Catalog\Helper\Image $productImageHelper,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    ) {
        $this->_resource = $resource;
        $this->_storeManager = $storeManager;
        $this->date = $date;
        $this->jsonHelper = $jsonHelper;
        $this->_transportBuilder = $transportBuilder;
        $this->_productImageHelper = $productImageHelper;
        $this->_inlineTranslation = $inlineTranslation;
        parent::__construct($context);
    }

    public function execute()
    {
            $time = $this->date->gmtDate();
            $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $tableName = $resource->getTableName('dotsquares_productfaq_items');
            $post  = $this->getRequest()->getParams();
			if ($post) {
			    $customername = $post['customer_name'];
                $customerquestion = $post['customer_question'];
                $model = $objectManager->create('Dotsquares\Productfaq\Model\Items')->getCollection()
                ->addFieldToFilter('product_id', $post['product_id'])
                ->addFieldToFilter('customer_email', $post['customer_email']);
                $data = $model->getData();
                /******************************* mail function *********************************/
                    $product_id = $post['product_id'];
                    $product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($product_id);
                    $product_name = $product->getName();
                    $product_url = $product->getProductUrl();
                    $product_image = $product->getImage();
                    $store = $this->_storeManager->getStore();
                    $imageUrl = $this->resizeImage($product, 'product_base_image', 200, 300)->getUrl();
					$email = $post['customer_email'];
                    $enable_adminmail = $this->_objectManager->create('Dotsquares\Productfaq\Helper\Data')->getConfig('productfaq/general/email_admin');
                    $mail_address = $this->_objectManager->create('Dotsquares\Productfaq\Helper\Data')->getConfig('productfaq/general/email_address_faq');
					$adminemaild = $this->_objectManager->create('Dotsquares\Productfaq\Helper\Data')->getConfig('trans_email/ident_general/email');
                    $to = array();
                    if($mail_address != ''){
                        $pos = strpos($mail_address, ',');
                        if ($pos==true) {
                            $to = explode(",",$mail_address);
                            $to[] = $email;
                            $to[] = $adminemaild;
                        } else {
                            $to[] = $mail_address;
                            $to[] = $email;
                            $to[] = $adminemaild;
                        }
                    }else{
                        $to[] = $email;
                        $to[] = $adminemaild;
                    }
					$templateOptions = array(
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId()
                    );
                    if($post['customer_question']){
                        $massage = 'Question: '.$post['customer_question'];
                    } else {
                        $massage = 'Question: Customer Question.';
                    }
                    $templateVars = array(
                        'store' => $this->_storeManager->getStore(),
                        'product_url'  => $product_url,
                        'product_name'  => $product_name,
                        'product_image'  => $imageUrl,
                        'massage' => $massage,
                    );
					$from = array(
                        'email' => $email,
                        'name' => 'Customer Question'
                    );
                    $this->_inlineTranslation->suspend();
                    $this->_transportBuilder->setTemplateIdentifier('productfaq_template')
                    ->setTemplateOptions($templateOptions)
                    ->setTemplateVars($templateVars)
                    ->setFrom($from);
                    foreach ($to as $email) {
						$this->_transportBuilder->addTo($email);
					}
                    $transport =  $this->_transportBuilder->getTransport();
                    try {
                        if ($enable_adminmail == 1) {
                            $transport->sendMessage();
                        }
                        $sql = 'Insert Into '. $tableName .' (`product_id`, `product_name`, `customer_email`, `customer_name`  ,`created_date` , `question`, `status`) VALUES ("'.$product_id.'", "'.$product_name.'","'.$email.'","'.$customername.'","'.$time.'","'.$customerquestion.'","2");';
                        $connection->query($sql);
                    } catch (\Exception $error) {
                        return false;
                    }
                    /********************************** End mail function**********************************************/
					$response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
					$response->setHeader('Content-type', 'text/plain');
                    $data1 = 'success';
                } else {
					$response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
					$response->setHeader('Content-type', 'text/plain');
					$data1 = 'error';
				}
				$response->setContents(
					$this->jsonHelper->jsonEncode(
						[
							'data' => $data1,
						]
					)
				);
        return $response;
    }
    public function resizeImage($product, $imageId, $width, $height = null)
    {
        $resizedImage = $this->_productImageHelper->init($product, $imageId)
            ->constrainOnly(true)
            ->keepAspectRatio(true)
            ->keepTransparency(true)
            ->keepFrame(false)
            ->resize($width, $height);
        return $resizedImage;
    }
}
