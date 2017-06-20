<?php
class VES_VendorsSubaccount_Block_Form_Element_Username extends Varien_Data_Form_Element_Text
{
	public function __construct($attributes=array())
	{
		parent::__construct($attributes);
		$this->setType('text');
	}
	public function getBeforeElementHtml(){
		return $this->getData('before_element_html');
	}
	public function getElementHtml()
    {
    	$html = $this->getBeforeElementHtml();
        $html.= '<input id="'.$this->getHtmlId().'" name="'.$this->getName()
             .'" value="'.$this->getEscapedValue().'" '.$this->serialize($this->getHtmlAttributes()).'/>'."\n";
        $html.= $this->getAfterElementHtml();
        return $html;
    }
}