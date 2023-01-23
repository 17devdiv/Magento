<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Adminhtml\Sellers;

use Dotsquares\Marketplace\Controller\Adminhtml\Sellers;

class Save extends Sellers {
    /**
     * Function to save Seller Data
     *
     * @return id(int)
     */
    public function execute() {
        $isPost = $this->getRequest ()->getPost ();
        if ($isPost) {
            $sellerModel = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
            $sellerId = $this->getRequest ()->getPost ( 'id' );
            if ($sellerId) {
                $sellerModel->load ( $sellerId );
            }
            $formData = $this->getRequest ()->getParam ( 'commission' );
            $sellerModel->setCommission ( $formData );
            try {
                $sellerModel->save ();
                // Display success message
                $this->messageManager->addSuccess ( __ ( 'Data has been saved.' ) );
                // Check if 'Save and Continue'
                if ($this->getRequest ()->getParam ( 'back' )) {
                    $this->_redirect ( '*/*/edit', [ 
                            'id' => $sellerModel->getId (),
                            '_current' => true 
                    ] );
                    return;
                }
                // Go to grid page
                $this->_redirect ( '*/*/' );
                return;
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
            $this->_getSession ()->setFormData ( $formData );
            $this->_redirect ( '*/*/edit', [ 
                    'id' => $sellerId 
            ] );
        }
    }
}