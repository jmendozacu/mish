<?php

class OTTO_AdvancedFaq_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('advancedfaq')->__('Answered'),
            self::STATUS_DISABLED   => Mage::helper('advancedfaq')->__('Pending')
        );
    }
    
    static public function getOptionShowArray()
    {
    	return array(
    			self::STATUS_ENABLED    => Mage::helper('advancedfaq')->__('Yes'),
    			self::STATUS_DISABLED   => Mage::helper('advancedfaq')->__('No')
    	);
    }
    
    static public function getStatusArray(){
    	return 
    	array(
              array(
                  'value'     => self::STATUS_ENABLED,
                  'label'     => Mage::helper('advancedfaq')->__('Enabled'),
              ),

              array(
                  'value'     => self::STATUS_DISABLED,
                  'label'     => Mage::helper('advancedfaq')->__('Disabled'),
              ),
          );
    }
    static public function getStatusShowArray(){
    	return
    	array(
    			array(
    					'value'     => self::STATUS_ENABLED,
    					'label'     => Mage::helper('advancedfaq')->__('Yes'),
    			),
    
    			array(
    					'value'     => self::STATUS_DISABLED,
    					'label'     => Mage::helper('advancedfaq')->__('No'),
    			),
    	);
    }
    static public function getStatusOption(){
    	return
    	array(
    			array(
    					'value'     => self::STATUS_ENABLED,
    					'label'     => Mage::helper('advancedfaq')->__('Answered'),
    			),
    	
    			array(
    					'value'     => self::STATUS_DISABLED,
    					'label'     => Mage::helper('advancedfaq')->__('Pending'),
    			),
    	);
    }
}