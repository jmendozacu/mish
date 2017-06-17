<?php

class Nanowebgroup_HybridMobile_Model_System_Config_Source_Dropdown_Cats
{
    public function toOptionArray()
    {

        $_categories = Mage::getModel('catalog/category')->getCollection()
              ->addAttributeToSelect('*')//or you can just add some attributes
              // ->addAttributeToFilter('level', 2)//2 is actually the first level
              ->addAttributeToFilter('is_active', 1)//if you want only active categories
          ;
        $cats = array();
        if ($_categories->getSize()) {
            foreach($_categories as $_category) {
              if ($_category->getName() == 'Default Category') {
                continue;
              }
                $cats[] = array(
                        'value' => (int)$_category->getId(),
                        'label' => (int)$_category->getId().'. '.$_category->getName(),
                    );
            }
        }

        return $cats;
    }
}