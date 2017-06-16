<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Welldone_Block_Adminhtml_Loadpresetconfig_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected $configsfolder;

    public function __construct()
    {
        parent::__construct();
        $this->configsfolder='app/code/local/Etheme/Welldone/etc/import/configsets/';
    }

    protected function _prepareForm()
    {
        $form_builder = new Varien_Data_Form();
        $fieldset = $form_builder->addFieldset('action_fieldset', array('legend'=>Mage::helper('welldone')->__('Choose demo presets for store')));

        $fieldset->addField('store_id', 'select', array(
            'name'      => 'store',
            'title'     => Mage::helper('cms')->__('Store View'),
            'label'     => Mage::helper('cms')->__('Store View'),
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            'note'=>'List of stores configured in administrator panel using default Magento features'
        ));


        $configsets=array();
        $configfiles=array();
        $configfiles=$configfiles=$this->helper('welldone')->ReadFolderDirectory(Mage::getBaseDir().'/'.$this->configsfolder.'/');;


        foreach($configfiles as $file=>$value)
        {
            $configxml=new Varien_Simplexml_Config(Mage::getBaseDir().'/'.$this->configsfolder.'/'.$value);
            $name=(string)$configxml->getNode('name');
            $configsets[]=array('value'=>str_replace('.xml','',$value),
                                'label'=>$name,
            );
        }
        asort($configsets);

        $fieldset->addField('configset', 'select', array(
            'name'      => 'configset',
            'class'     => 'ms_selectbox',
            'title'     => Mage::helper('cms')->__('Install Demo'),
            'label'     => Mage::helper('cms')->__('Install Demo'),
            'values'    => $configsets,
            'value'=>'default'
        ));


        $form_builder->setMethod('post');
        $form_builder->setAction($this->getUrl('*/*/configinstall'));
        $form_builder->setUseContainer(true);
        $form_builder->setId('edit_form');
        $this->setForm($form_builder);
        
        return parent::_prepareForm();
    }



}
