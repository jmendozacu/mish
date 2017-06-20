<?php
/**
 * @copyright  Copyright (c) 2009 Ashley Schroder
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Ashley Schroder
 */

class OTTO_AdvancedFaq_Model_System_Config_Source_Recapcha_Theme
{
	
	public function toOptionArray()
    {
    	// There are 3 possibilities: PLAIN, LOGIN and CRAM-MD5, plus no authentication
    	// http://framework.zend.com/manual/en/zend.mail.smtp-authentication.html
        return array(
        	"red"   => Mage::helper('adminhtml')->__('Red'),
            "white"   => Mage::helper('adminhtml')->__('White'),
            "blackglass"   => Mage::helper('adminhtml')->__('Blackglass'),
            "clean"   => Mage::helper('adminhtml')->__('Clean'),
        	"custom"   => Mage::helper('adminhtml')->__('Custom')
        );
    }
}
