<?php
class Cartin24_Cmsimport_Block_Adminhtml_Cms_Block extends Mage_Adminhtml_Block_Cms_Block
{
    public function __construct()
    {
        parent::__construct();

        $this->_addButton('importcsv', array(
        		'label'     => 'Import CSV',
        		'onclick'   => "setLocation('".$this->getUrl('cmsimport/adminhtml_block/upload')."')",
        		'class'     => 'import'
        ));
        

    }  

}
