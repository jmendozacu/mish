<?php
class VES_VendorsCms_Block_Form_Element_Layout extends Varien_Data_Form_Element_Editor
{   
	public function getTranslationJSON(){
		$helper = Mage::helper('vendorscms');
		return json_encode(array(
			'REMOVE_LAYOUT_UPDATE'	=> $helper->__('Remove Layout Update'),
			'DISPLAY_ON'			=> $helper->__('Display On'),
			'PLEASE_SELECT'			=> $helper->__('-- Please Select --'),
		));
	}
	
	public function getAppPageGroupJSON(){
		$pageGroups = Mage::helper('vendorscms')->getAppPageGroups();
		return json_encode($pageGroups);
	}
	
	
	public function getHtml()
    {
        $html = '<tr>
			    <td class="value" colspan="2"><div id="layout_container" class="app_layout_container">&nbsp;</div></td>
			    </tr>'.
			    '<script type="text/javascript">
			    	var appPageGroups = '.$this->getAppPageGroupJSON().';
			    	var translation = '.$this->getTranslationJSON().';
			    	var frontendInstance = new LayoutUpdate("layout_container",appPageGroups,translation);
			    </script>'.
			   	'';
        return $html;
    }

}