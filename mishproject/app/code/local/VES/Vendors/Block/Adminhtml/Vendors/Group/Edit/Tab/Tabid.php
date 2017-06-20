<?php
 
class VES_Vendors_Block_Adminhtml_Vendors_Group_Edit_Tab_Tabid extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ves_vendors/assigncategory.phtml');
    }
}