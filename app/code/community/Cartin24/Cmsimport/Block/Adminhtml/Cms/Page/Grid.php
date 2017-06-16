<?php
class Cartin24_Cmsimport_Block_Adminhtml_Cms_Page_Grid extends Mage_Adminhtml_Block_Cms_Page_Grid {

	protected function _prepareColumns() {
		parent::_prepareColumns();
		$this->addExportType('cmsimport/adminhtml_page/exportCsv', Mage::helper('cmsimport')->__('CSV'));
		return $this;

	}


}
