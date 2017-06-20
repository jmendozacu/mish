<?php

class Eadesigndev_Romcity_Block_Adminhtml_Romcity_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("romcity_form", array("legend" => Mage::helper("romcity")->__("Item information")));

        $countries = Mage::getSingleton('directory/country')->getResourceCollection()->loadByStore()->toOptionArray(false);

        $fieldset->addField('country_id', 'select', array(
            'label' => Mage::helper('romcity')->__('Country'),
            'values' => $countries, //Eadesigndev_Romcity_Block_Adminhtml_Romcity_Grid::getValueArray1(),
            'name' => 'country_id',
            "class" => "required-entry",
            "required" => true,
        ));
        $fieldset->addField('region_id', 'select', array(
            'label' => Mage::helper('romcity')->__('State'),
            'values' => Eadesigndev_Romcity_Block_Adminhtml_Romcity_Grid::getValueArray2('CL'),
            'name' => 'region_id',
            "class" => "required-entry",
            "required" => true,
        ));
        $fieldset->addField("cityname", "text", array(
            "label" => Mage::helper("romcity")->__("City"),
            "class" => "required-entry",
            "required" => true,
            "name" => "cityname",
        ));


        if (Mage::getSingleton("adminhtml/session")->getRomcityData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getRomcityData());
            Mage::getSingleton("adminhtml/session")->setRomcityData(null);
        } elseif (Mage::registry("romcity_data")) {
            $form->setValues(Mage::registry("romcity_data")->getData());
        }
        return parent::_prepareForm();
    }

}
