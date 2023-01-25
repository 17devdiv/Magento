<?php
namespace Magenest\Salesforce\Controller\Adminhtml\Sync;

use Magenest\Salesforce\Model\Sync;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

/**
 * Class Account
 * @package Magenest\Salesforce\Controller\Adminhtml\Sync
 */
class Account extends Action
{
    /**
     * @var Sync\Account
     */
    protected $_account;

    /**
     * Customer constructor.
     * @param Context $context
     * @param Sync\Account $account
     */
    public function __construct(
        Context $context,
        Sync\Account $account
    ) {
        $this->_account = $account;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $customerId = $this->getRequest()->getParam('id');
        try {
            if ($customerId) {
                $this->_account->sync($customerId, true);
                $this->messageManager->addSuccessMessage(
                    __('Account sync process complete, check out Reports for result.')
                );
            } else {
                $this->messageManager->addNoticeMessage(
                    __('No account has been selected')
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something happen during syncing process. Detail: ' . $e->getMessage())
            );
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('customer/index/edit', ['id' => $customerId]);
        return $resultRedirect;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Salesforce::config_salesforce');
    }
}
