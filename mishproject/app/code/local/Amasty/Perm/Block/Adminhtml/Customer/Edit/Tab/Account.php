<?php
/**
 * Copyright (c) 2009-2011 Amasty (http://www.amasty.com)
 */
class Amasty_Perm_Block_Adminhtml_Customer_Edit_Tab_Account extends Mage_Adminhtml_Block_Customer_Edit_Tab_Account
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setForm(Varien_Data_Form $form)
    {
        $allowedGroup = $this->_getAllowedGroup();
        if ($allowedGroup){
            $fld = $form->getElement('group_id');
            $label = '';
            foreach ($fld->getValues() as $option){
                if ($option['value'] == $allowedGroup){
                    $label = $option['label'];
                }
            }
            $fld->setValues(array($allowedGroup => $label));
        }
        return parent::setForm($form);
    }
    
    protected function _getAllowedGroup()
    {
        $user = Mage::getSingleton('admin/session')->getUser();  
        if (!$user)
            return 0;
        
        if (!Mage::helper('amperm')->isSalesPerson($user)){
            return 0;
        }   

        return $user->getCustomerGroupId();         
    }

}