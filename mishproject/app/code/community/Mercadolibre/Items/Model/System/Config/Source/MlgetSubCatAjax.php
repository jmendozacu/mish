<?php
class Mercadolibre_Items_Model_System_Config_Source_MlgetSubCatAjax extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('items/mlgetallsubcatajax/button.phtml');
    }
 
    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }
 
    /**
     * Return ajax url for button
     *
     * @return string
     */
    public function getAjaxCheckUrl()
    {
        return Mage::helper('adminhtml')->getUrl('items/index/getMLSubCatAjax/');
    }
 
    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
		$storeGetId = $this->getRequest()->getParam('store',Mage::helper('items')-> getMlDefaultStoreId());
		$data = Mage::app()->getStore($storeGetId);
		$storeId = $data->getData('store_id');
		
		
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
            'id'        => 'mlGetAllSubCatButt',
            'label'     => $this->helper('adminhtml')->__('Get Subcategories'),
            'onclick'   => 'javascript:checksubcat(\''.$storeId.'\'); return false;'
        ));
 
        return $button->toHtml();
    }
}
