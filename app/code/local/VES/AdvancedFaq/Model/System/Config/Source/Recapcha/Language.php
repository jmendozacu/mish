<?php
/**
 * @copyright  Copyright (c) 2009 Ashley Schroder
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Ashley Schroder
 */

class OTTO_AdvancedFaq_Model_System_Config_Source_Recapcha_Language
{
	
	public function toOptionArray()
    {
    	// There are 3 possibilities: PLAIN, LOGIN and CRAM-MD5, plus no authentication
    	// http://framework.zend.com/manual/en/zend.mail.smtp-authentication.html
        return array(
        	"en"   => Mage::helper('adminhtml')->__('English'),
            "nl"   => Mage::helper('adminhtml')->__('Dutch'),
            "fr"   => Mage::helper('adminhtml')->__('French'),
            "de"   => Mage::helper('adminhtml')->__('German'),
        	"pt"   => Mage::helper('adminhtml')->__('Portuguese'),
        	"ru"   => Mage::helper('adminhtml')->__('Russian'),
        	"es"   => Mage::helper('adminhtml')->__('Spanish'),
        	"tr"   => Mage::helper('adminhtml')->__('Turkish'),
        );
    }
}
