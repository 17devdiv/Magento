<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */

namespace Dotsquares\Offerbanner\Controller\Adminhtml\Items;
use Magento\Framework\App\Filesystem\DirectoryList;
class Save extends \Dotsquares\Offerbanner\Controller\Adminhtml\Items
{
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                $model = $this->_objectManager->create('Dotsquares\Offerbanner\Model\Items');
                $data = $this->getRequest()->getPostValue();
				//print_r($data); die("test");
                $inputFilter = new \Zend_Filter_Input(
                    [],
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();
                $id = $this->getRequest()->getParam('id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                    }
                }
				
				$imageRequest = $this->getRequest()->getFiles('image');
            if ($imageRequest) {
				
                if (isset($imageRequest['name'])) {
                    $fileName = $imageRequest['name'];

                } else {
                    $fileName = '';
                }
            } else {
                $fileName = '';
            }
				
				
				
				
				 if ($imageRequest && strlen($fileName)) 
				{
					
					/* * Save image upload */
					try
					{
						$uploader = $this->_objectManager->create(
							'Magento\MediaStorage\Model\File\Uploader',
							['fileId' => 'image']
						);
						$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
						/** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
						$imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
						$uploader->addValidateCallback('image', $imageAdapter, 'validateUploadFile');
						$uploader->setAllowRenameFiles(true);
						$uploader->setFilesDispersion(true);

						/** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
						$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
							->getDirectoryRead(DirectoryList::MEDIA);
						$result = $uploader->save(
							$mediaDirectory->getAbsolutePath('slider')
						);

						$data['image'] = $result['file'];
					} catch (\Exception $e) {
						if ($e->getCode() == 0) {
							$this->messageManager->addError($e->getMessage());
						}
					}
				}
				
				else {
                if (isset($data['image']) && isset($data['image']['value'])) {
                    if (isset($data['image']['delete'])) {
                        $data['image'] = null;
                        $data['delete_image'] = true;
                    } elseif (isset($data['image']['value'])) {
                        $data['image'] = $data['image']['value'];
                    } else {
                        $data['image'] = null;
                    }
                }
            }
				
				
				
				
				
                $model->setData($data);
                $session = $this->_objectManager->get('Magento\Backend\Model\Session');
                $session->setPageData($model->getData());
                $model->save();
                $this->messageManager->addSuccess(__('You saved the item.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('dotsquares_offerbanner/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('dotsquares_offerbanner/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('id');
                if (!empty($id)) {
                    $this->_redirect('dotsquares_offerbanner/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('dotsquares_offerbanner/*/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                $this->_redirect('dotsquares_offerbanner/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->_redirect('dotsquares_offerbanner/*/');
    }
}
