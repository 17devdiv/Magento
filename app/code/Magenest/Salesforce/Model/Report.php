<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Salesforce extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_Salesforce
 * @author   ThaoPV
 */

namespace Magenest\Salesforce\Model;

use Magento\Backend\Model\Auth\Session;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Report
 * @package Magenest\Salesforce\Model
 */
class Report extends AbstractModel
{
    /**
     * Core Date
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_coreDate;

    /**
     * Session Admin
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * Session Customer
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * Report constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param DateTime $coreDate
     * @param Session $backendAuthSession
     * @param CustomerSession $customerSession
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DateTime $coreDate,
        Session $backendAuthSession,
        CustomerSession $customerSession,
        \Magento\Framework\App\RequestInterface $request
    ){
        $this->_coreDate = $coreDate;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_customerSession = $customerSession;
        $this->_request = $request;
        parent::__construct($context, $registry);
    }

    /**
     * Initialize resources
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Magenest\Salesforce\Model\ResourceModel\Report');
    }

    /**
     * @param $id
     * @param $action
     * @param $table
     * @param int $status
     * @param null $message
     * @param null $mid
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveReport($id, $action, $table, $status = 1, $message = null, $mid = null)
    {
        $datetime = $this->_coreDate->gmtDate();
        $admin_user = $this->_backendAuthSession->getUser();
        $current_user = $this->_customerSession->getCustomer();
        if ($admin_user) {
            $name = $admin_user->getName();
            $email = $admin_user->getEmail();
        } elseif ($current_user->getName()) {
            $name = $current_user->getName();
            $email = $current_user->getEmail();
            if(empty($email)){
                $params = $this->_request->getParams();
                $firstname = '';
                if(isset($params['firstname'])){
                    $firstname = $params['firstname'];
                }
                $lastname = '';
                if(isset($params['lastname'])){
                    $lastname = $params['lastname'];
                }
                $name = $firstname . " " . $lastname ;
                $email = isset($params['email']) ? $params['email'] : '';
            }
        }
        if(empty($email)) {
            $name = "Guest";
            $email = '';
        }

        $data = [
            'record_id' => $id,
            'magento_id' => $mid,
            'action' => $action,
            'salesforce_table' => $table,
            'datetime' => $datetime,
            'username' => $name,
            'email' => $email,
            'status' => $status,
            'msg' => $message
        ];
        $this->setData($data);
        $this->save();
        return;
    }

    /**
     * @param $reports
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveReports($reports)
    {
        if ($reports == [])
            return;
        $resource = $this->getResource();
        $resource->getConnection()->insertMultiple(
            $resource->getMainTable(),
            $reports
        );
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getInfoReport()
    {
        $result = [];
        $result['datetime'] = $this->_coreDate->gmtDate();
        $admin_user = $this->_backendAuthSession->getUser();
        $current_user = $this->_customerSession->getCustomer();
        if ($admin_user) {
            $name = $admin_user->getName();
            $email = $admin_user->getEmail();
        } elseif ($current_user->getName()) {
            $name = $current_user->getName();
            $email = $current_user->getEmail();
        } else {
            $name = "Guest";
            $email = '';
        }
        $result['username'] = $name;
        $result['email'] = $email;
        return $result;
    }

    /**
     * @param $collectionArr
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteMultiReports($collectionArr)
    {
        if (empty($collectionArr)) {
            return;
        }
        $collectionIds = implode(', ', $collectionArr);
        $this->getResource()->getConnection()->delete(
            $this->getResource()->getMainTable(),
            "{$this->getIdFieldName()} in ({$collectionIds})"
        );
    }
}
