<?php
class Cartin24_Cmsimport_Block_Adminhtml_Cms_Page extends Mage_Adminhtml_Block_Cms_Page
{
	public function __construct()
		{
			parent::__construct();

			$this->_addButton('importcsv', array(
					'label'     => 'Import CSV',
					'onclick'   => "setLocation('".$this->getUrl('cmsimport/adminhtml_page/upload')."')",
					'class'     => 'import'
			));
			

		}  

}
