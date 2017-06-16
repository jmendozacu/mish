<?php
class Cartin24_Cmsimport_Block_Adminhtml_Cms_Block_Grid extends Mage_Adminhtml_Block_Cms_Block_Grid {


	protected function _prepareColumns() {
		parent::_prepareColumns();

		$this->addExportType('cmsimport/adminhtml_block/exportCsv', Mage::helper('cmsimport')->__('CSV'));
		//$this->addExportType('*/*/exportXml', Mage::helper('cmsimport')->__('XML'));
		return $this;

	} 


}
