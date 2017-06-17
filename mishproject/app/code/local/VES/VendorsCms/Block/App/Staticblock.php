<?php

/**
 * Vendor Cms app static block
 *
 * @category   VES
 * @package    VES_VendorCms
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsCms_Block_App_Staticblock extends Mage_Core_Block_Abstract
{
	public function isEnabled(){
		$result = new Varien_Object(array('is_enabled'=>true));
		Mage::dispatchEvent('ves_vendorscms_app_type_staticblock',array('result'=>$result));
		return $result->getIsEnabled();
	}
	public function setApp($app){
		$this->setData('app',$app);
		$blockOptions = $app->getOptionsByCode('block_app_option');
		if(sizeof($blockOptions)){
			$optionValue 	= json_decode($blockOptions[0]->getValue(),true);
			$staticBlockId	= $optionValue['block_id'];
			$this->setBlockId($staticBlockId);
		}
	}
	
	/**
     * Prepare Content HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
    	if(!$this->isEnabled()) return '';
    			
        $blockId = $this->getBlockId();
        $html = '';
        if ($blockId) {
            $block = Mage::getModel('vendorscms/block')
                ->setVendorId(Mage::registry('vendor')->getId())
                ->loadBlock($blockId);
            if ($block->getIsActive()) {
                /* @var $helper Mage_Cms_Helper_Data */
                $processor = Mage::getModel('vendorscms/template_filter');
                $html = $processor->filter($block->getContent());
                $this->addModelTags($block);
            }
        }
        return $html;
    }
}
