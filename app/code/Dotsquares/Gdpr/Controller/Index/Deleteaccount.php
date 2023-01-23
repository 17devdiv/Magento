<?php
/**
 * Dotsquares
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @author Dotsquares
 * @package Dotsquares_GDPR
 * @copyright Copyright (c) Dotsquares (https://www.dotsquares.com/)
 */

namespace Dotsquares\Gdpr\Controller\Index; 

use \Magento\Customer\Model\Session;
use Magento\Framework\Controller\ResultFactory;

class Deleteaccount extends \Magento\Framework\App\Action\Action {

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Dotsquares\Gdpr\Helper\Data $helper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Sales\Model\Order\AddressFactory $addressFactory,
        \Magento\Customer\Model\AddressFactory $customeraddressFactory,
        \Magento\Sales\Model\OrderFactory $ordercollection,
        \Magento\Framework\Registry $registry,
        \Magento\Review\Model\ReviewFactory $reviewCollection,
        Session $customerSession
    ){
        $this->_customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->mathRandom = $mathRandom;
		$this->_messageManager = $context->getMessageManager();
        $this->_orderFactory = $ordercollection;
        $this->reviewCollection = $reviewCollection;
        $this->_customerFactory = $customerFactory;
        $this->addressFactory = $addressFactory;
        $this->customeraddressFactory = $customeraddressFactory;
        $this->_helper = $helper;
        $this->_registry = $registry;
        parent::__construct($context);
    }

    public function execute()
    {
		if(!$this->_customerSession->isLoggedIn()){
			return $this->_redirect('customer/account/login/');
		}
        $willcustomerdata = $this->_helper->getConfig('cusotmerdelete/general/willcustomerdata');
        $customer_id = $this->_customerSession->getCustomerId();
        $customer = $this->_customerFactory->create()->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('entity_id', array('eq' => $customer_id))->load();
        foreach($customer as $customeremail){
            $customer_email = $customeremail->getEmail();
            break;
        }
		$delete_data = explode(",",$willcustomerdata);
       	if((in_array('customer',$delete_data)) && (in_array('order',$delete_data))){
			$this->deleteorder($customer_id);
			$this->deletecustomer($customer);
		}else if(in_array('order',$delete_data)){
			$this->deleteorder($customer_id);
		}else if(in_array('customer',$delete_data)){
			$this->deletecustomer($customer);
		}else{
			$this->_messageManager->addSuccess(__("Nothing to deleted."));
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
		}
    }

    public function deletecustomer($deletecustomer){
		$customer_id = $this->_customerSession->getCustomerId();
		$customer_email = $this->_customerSession->getCustomer()->getEmail();
		$news_subscriber = $this->_objectManager->create('Magento\Newsletter\Model\Subscriber')->loadByEmail($customer_email);
		$ordercollection = $this->_orderFactory->create()->getCollection()->addFieldToFilter('customer_id', $customer_id);
		$ordercollection->addAttributeToSelect('*')->addAttributeToFilter('status', array('in' => array('processing','pending')));
		$order_pending = $ordercollection->getSize();
		if($order_pending < 1){
		    try{
		        $this->_registry->register('isSecureArea', true);
				if($news_subscriber->getId() != ''){
		        	$news_subscriber->delete();
		        }
		        $deletecustomer->delete();
		    	$this->_messageManager->addSuccess(__("Your account has been successfully deleted from the website now."));
                return $this->_redirect('customer/account/login/');
    	    } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t delete your account.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                return $this->_redirect('*/*/');
            }
		}
		$this->_messageManager->addError(__('We can\'t delete your account because you have pending/processing order on our website. Once the order is complete and fully processed then you will be able to delete your account.'));
		return $this->_redirect('*/*/');
	}
	
    public function deleteorder($customer_id){
        $checkpending_order = $this->_orderFactory->create()->getCollection()->addFieldToFilter('customer_id', $customer_id);
		$checkpending_order->addAttributeToSelect('*')->addAttributeToFilter('status', array('in' => array('processing','pending')));
		$delete_order = $checkpending_order->getSize();
		if($delete_order < 1):
			$ordercollection = $this->_orderFactory->create()->getCollection()->addFieldToFilter('customer_id', $customer_id);
		    $timestamp = $this->_helper->getTimestamp();
		    $customer_name = $this->_helper->getConfig('cusotmerdelete/general/customer_name');
		    $customer_email = $this->_helper->getConfig('cusotmerdelete/general/customer_email');
		    $customer_tel = $this->_helper->getConfig('cusotmerdelete/general/customer_tel');
		    if($customer_name){
		    	$uniqename = $customer_name;
		    }else{
		    	$uniqename = $timestamp;
		    }
		    if($customer_email){
		    	$uniqemail = $customer_email;
		    }else{
		    	$uniqemail = $timestamp.'@change.com';
		    }
		    if($customer_tel){
		    	$telephone = $customer_tel;
		    }else{
		    	$telephone = $timestamp;
		    }
		    try {
                foreach($ordercollection as $order){
                    $billingId = $order->getBillingAddressId();
                    if($billingId != ''){
                        $this->changebilling($billingId);
                    }
                    $shippingAddressId = $order->getShippingAddressId();
                    $this->changeshipping($shippingAddressId);
                    $order->setCustomerFirstname($uniqename)->setCustomerLastname($uniqename)->setCustomerEmail($uniqemail)->save();
                }
				$this->_messageManager->addSuccess(__("All your data has been Successfully deleted from the website."));
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t delete your orders.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                return $this->_redirect('*/*/');
            }
		endif;
    }

    public function changebilling($billing_id){
        $timestamp = $this->_helper->getTimestamp();
		$customer_name = $this->_helper->getConfig('cusotmerdelete/general/customer_name');
		$customer_email = $this->_helper->getConfig('cusotmerdelete/general/customer_email');
		$customer_tel = $this->_helper->getConfig('cusotmerdelete/general/customer_tel');
		if($customer_name){
			$uniqename = $customer_name;
		}else{
			$uniqename = $timestamp;
		}
		if($customer_tel){
			$telephone = $customer_tel;
		}else{
			$telephone = $timestamp;
		}
        try {
            $billingaddress = $this->addressFactory->create()->load($billing_id);
            $billingaddress->setFirstname($uniqename);
            $billingaddress->setLastname($uniqename);
            $billingaddress->setTelephone($telephone);
            $billingaddress->save();
            return true;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t change customer\'s billing address.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return $this->_redirect('*/*/');
        }
    }

    public function changeshipping($shipping_id){
        try{
            $shippingaddress = $this->addressFactory->create()->load($shipping_id);
            $timestamp = $this->_helper->getTimestamp();
		    $customer_name = $this->_helper->getConfig('cusotmerdelete/general/customer_name');
		    $customer_email = $this->_helper->getConfig('cusotmerdelete/general/customer_email');
		    $customer_tel = $this->_helper->getConfig('cusotmerdelete/general/customer_tel');
		    if($customer_name){
		    	$uniqename = $customer_name;
		    }else{
		    	$uniqename = $timestamp;
		    }
		    if($customer_tel){
		    	$telephone = $customer_tel;
		    }else{
		    	$telephone = $timestamp;
		    }
            $shippingaddress->setFirstname($uniqename);
            $shippingaddress->setLastname($uniqename);
            $shippingaddress->setTelephone($telephone);
            $shippingaddress->save();
            return true;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t change customer\'s billing address.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return $this->_redirect('*/*/');
        }
    }

    public function deleteproductreview($customer_id)
    {
        $review = $this->reviewCollection->create()->getCollection()->addFieldToFilter('customer_id', $customer_id);
    }
}