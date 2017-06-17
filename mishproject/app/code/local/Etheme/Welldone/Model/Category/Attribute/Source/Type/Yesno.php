<?php

class Etheme_Welldone_Model_Category_Attribute_Source_Type_Yesno
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{    
    /**
     * Get list of available block column proportions
     */
    public function getAllOptions()
    {
        if (!$this->_options)
        {
            $this->_options = array(
                array('value' => 1,     'label' => 'Yes'),
                array('value' => 0,     'label' => 'No'),

            );
        }
        return $this->_options;
    }
}
