<?php

namespace Dotsquares\Productfaq\Controller\Adminhtml\Items;

use Magento\Framework\App\Action\Context;

class Save extends \Magento\Framework\App\Action\Action
{
    protected $_storeManager;
    protected $_productImageHelper;
    protected $_transportBuilder;
    protected $_inlineTranslation;
    protected $messageManager;
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $_storeManager,
        \Magento\Catalog\Helper\Image $_productImageHelper,
        \Magento\Framework\Translate\Inline\StateInterface $_inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $_transportBuilder,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_storeManager = $_storeManager;
        $this->_productImageHelper = $_productImageHelper;
        $this->_inlineTranslation = $_inlineTranslation;
        $this->_transportBuilder = $_transportBuilder; 
        $this->messageManager = $messageManager;
        parent::__construct($context);
    }
    public function execute()
    {
        $enable_usermail = $this->_objectManager->create('Dotsquares\Productfaq\Helper\Data')->getConfig('productfaq/general/email_user');
        $adminemaild = $this->_objectManager->create('Dotsquares\Productfaq\Helper\Data')->getConfig('trans_email/ident_general/email');
        if ($this->getRequest()->getPostValue()) {
            try {
                $model = $this->_objectManager->create('Dotsquares\Productfaq\Model\Items');
                $data = $this->getRequest()->getPostValue();
                $inputFilter = new \Zend_Filter_Input(
                    [],
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();
                $id = $this->getRequest()->getParam('id');
                $answer = $this->getRequest()->getParam('answer');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                    }
                }
                $productid = $model->getProductId();
                $question = 'Question: '.$model->getQuestion();
                $customer_mail = $model->getCustomerEmail();
                $product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($productid);
                $product_name = $product->getName();
                $product_url = $product->getProductUrl();
                $product_image = $product->getImage();
                $store = $this->_storeManager->getStore();
                $imageUrl = $this->resizeImage($product);
                $templateOptions = array(
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId()
                );
                if($answer){
                    $admin_answer = 'Answer: '.$answer;
                }else{
                    $admin_answer = 'Answer: Sry';
                }
                $templateVars = array(
                    'store' => $this->_storeManager->getStore(),
                    'product_url'  => $product_url,
                    'product_name'  => $product_name,
                    'product_image'  => $imageUrl,
                    'question' => $question,
                    'answer' => $admin_answer,
                );
                $from = array(
                    'email' => $adminemaild,
                    'name' => 'Answers'
                );					

                $this->_inlineTranslation->suspend();
                $to = array($customer_mail);
                $transport = $this->_transportBuilder->setTemplateIdentifier('productfaq_reply_template')
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($to)
                ->getTransport();
                if($enable_usermail == 1){
                    $transport->sendMessage();
                }
                $model->setData($data);
                $session = $this->_objectManager->get('Magento\Backend\Model\Session');
                $session->setPageData($model->getData());
                $model->save();
                $this->messageManager->addSuccess(__('You saved the item.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('dotsquares_productfaq/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('dotsquares_productfaq/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('id');
                if (!empty($id)) {
                    $this->_redirect('dotsquares_productfaq/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('dotsquares_productfaq/*/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                $this->_redirect('dotsquares_productfaq/*edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->_redirect('dotsquares_productfaq/*/');
    }

    public function resizeImage($product)
    {
        $imageHelper = $this->_productImageHelper->init($product, 'product_listing_thumbnail_preview');
        $resizedImage = $imageHelper->getUrl(); 
        return $resizedImage;
    }
}
