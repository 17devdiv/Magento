<?php
namespace Dotsquares\Offerbanner\Block\Adminhtml\Helper\Renderer;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $_storeManager;
   
    protected $sliderModel;

    
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Dotsquares\Offerbanner\Model\Items $sliderModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->sliderModel = $sliderModel;
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        $storeViewId = $this->getRequest()->getParam('store');
        $logo = $this->sliderModel->load($row->getId());
        $srcImage = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . 'slider/'.$logo->getImage();

        return '<image width="100" height="100" src ="'.$srcImage.'" alt="'.$logo->getImage().'" title="'.$logo->getTitle().'" >';
    }
}
