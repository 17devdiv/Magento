<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Bannerslider
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Dotsquares\Offerbanner\Block\Adminhtml\Items;



/**
 * Banner grid.
 * @category Magestore
 * @package  Magestore_Bannerslider
 * @module   Bannerslider
 * @author   Magestore Developer
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
   public function __construct()
		{
				parent::__construct();
				$this->setId("offerbannerGrid");
				$this->setDefaultSort("offer_banner_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("offerbanner/offerbanner")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("offer_banner_id", array(
				"header" => Mage::helper("offerbanner")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "offer_banner_id",
				));
                
				$this->addColumn("title", array(
				"header" => Mage::helper("offerbanner")->__("Title"),
				"index" => "title",
				));
				$this->addColumn("url", array(
				"header" => Mage::helper("offerbanner")->__("Url"),
				"index" => "url",
				));
					$this->addColumn('start_date', array(
						'header'    => Mage::helper('offerbanner')->__('Start date'),
						'index'     => 'start_date',
						'type'      => 'date',
						'filter' => false,
					));
					
					$this->addColumn('end_date', array(
						'header'    => Mage::helper('offerbanner')->__('End date'),
						'index'     => 'end_date',
						'type'      => 'date',
						'filter' => false,
					));
					
						$this->addColumn('status', array(
						'header' => Mage::helper('offerbanner')->__('Status'),
						'index' => 'status',
						'type' => 'options',
						'options'=>Dotsquares_Offerbanner_Block_Adminhtml_Offerbanner_Grid::getOptionArray5(),				
						));
						
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('offer_banner_id');
			$this->getMassactionBlock()->setFormFieldName('offer_banner_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_offerbanner', array(
					 'label'=> Mage::helper('offerbanner')->__('Remove Offerbanner'),
					 'url'  => $this->getUrl('*/adminhtml_offerbanner/massRemove'),
					 'confirm' => Mage::helper('offerbanner')->__('Are you sure?')
				));
			return $this;
		}
			
		static public function getOptionArray5()
		{
            $data_array=array(); 
			$data_array[0]='Active';
			$data_array[1]='Deactive';
            return($data_array);
		}
		static public function getValueArray5()
		{
            $data_array=array();
			foreach(Dotsquares_Offerbanner_Block_Adminhtml_Offerbanner_Grid::getOptionArray5() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		} 
}
