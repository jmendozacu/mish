<?php

class VES_VendorsRma_Block_Adminhtml_Status_Edit_Tab_Template extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ves_vendorsrma/widget/form.phtml');
        $this->setDestElementId('edit_form');
        $this->setShowGlobalIcon(false);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

     //   Varien_Data_Form::setFieldsetRenderer(
           // $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')
      //  );

        return Mage_Adminhtml_Block_Widget::_prepareLayout();
    }
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('status_template', array('legend'=>Mage::helper('vendorsrma')->__('Store Templates')));

        $fieldset->addField('template_notify_customer', 'textarea', array(
            'label'     => Mage::helper('vendorsrma')->__('Notification sent to customer (leave blank not to send)'),
            'required'  => false,
            'name'      => 'template_notify_customer[]',
        ));


        $fieldset->addField('template_notify_admin', 'textarea', array(
            'label'     => Mage::helper('vendorsrma')->__('Notification sent to administrator (leave blank not to send)'),
            'required'  => false,
            'name'      => 'template_notify_admin[]',
        ));

        $fieldset->addField('template_notify_vendor', 'textarea', array(
            'label'     => Mage::helper('vendorsrma')->__('Notification sent to vendor (leave blank not to send)'),
            'required'  => false,
            'name'      => 'template_notify_vendor[]',
        ));

        $fieldset->addField('template_notify_history', 'textarea', array(
            'label'     => Mage::helper('vendorsrma')->__('Notification sent to messages history (leave blank not to send)'),
            'required'  => false,
            'name'      => 'template_notify_history[]',
        ));

        $this->setChild('form_after',
            $this->getLayout()->createBlock('vendorsrma/adminhtml_status_edit_tab_tmp')
        );

        if ( Mage::getSingleton('adminhtml/session')->getStatusData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getStatusData());
            Mage::getSingleton('adminhtml/session')->setStatusData(null);
        } elseif ( Mage::registry('status_data') ) {
            $form->setValues(Mage::registry('status_data')->getData());
            $templates = Mage::getModel("vendorsrma/template")->getCollection()->addFieldToFilter("status_id",Mage::registry('status_data')->getId());
            $i = 0;
            foreach($templates as $template ){
                 if($i != 0) $this->addFieldSetEdit($form,$template->getId(),$template->getData());
                 else $form->setValues($template->getData());
                 $i++;
            }
        }
        return parent::_prepareForm();
    }

    public function addFieldSetEdit($form,$id,$data){
        $fieldset = $form->addFieldset('status_template_'.$id, array('legend'=>Mage::helper('vendorsrma')->__('Store Templates')));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $field =$fieldset->addField('store_id_'.$id, 'select', array(
                'name'      => 'store_id[]',
                'label'     => Mage::helper('vendorsrma')->__('Store View'),
                'title'     => Mage::helper('vendorsrma')->__('Store View'),
                'class'     => 'required-entry req',
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, false),
                'value' => $data["store_id"]
            ));
        }
        else {
            $fieldset->addField('store_id_'.$id, 'hidden', array(
                'name' => 'store_id[]',
                'value' => $data["store_id"]
            ));
            Mage::registry('type_data')->setStoreId(Mage::app()->getStore(true)->getId());
        }


        $fieldset->addField('title_'.$id, 'text', array(
            'label'     => Mage::helper('vendorsrma')->__('Title'),
            'class'     => 'required-entry req',
            'required'  => true,
            'name'      => 'title[]',
            'value' => $data["title"]
        ));

        $fieldset->addField('template_notify_customer_'.$id, 'textarea', array(
            'label'     => Mage::helper('vendorsrma')->__('Notification sent to customer (leave blank not to send)'),
            'required'  => false,
            'name'      => 'template_notify_customer[]',
            'value' => $data["template_notify_customer"]
        ));


        $fieldset->addField('template_notify_admin_'.$id, 'textarea', array(
            'label'     => Mage::helper('vendorsrma')->__('Notification sent to administrator (leave blank not to send)'),
            'required'  => false,
            'name'      => 'template_notify_admin[]',
            'value' => $data["template_notify_admin"]
        ));

        $fieldset->addField('template_notify_vendor_'.$id, 'textarea', array(
            'label'     => Mage::helper('vendorsrma')->__('Notification sent to vendor (leave blank not to send)'),
            'required'  => false,
            'name'      => 'template_notify_vendor[]',
            'value' => $data["template_notify_vendor"]
        ));

        $fieldset->addField('template_notify_history_'.$id, 'textarea', array(
            'label'     => Mage::helper('vendorsrma')->__('Notification sent to messages history (leave blank not to send)'),
            'required'  => false,
            'name'      => 'template_notify_history[]',
            'value' => $data["template_notify_history"]
        ));


        $field=$fieldset->addField('submit_'.$id, 'text', array(
            'value'  => 'Submit',
            'tabindex' => 1
        ));
        $field->setRenderer($this->getLayout()->createBlock('vendorsrma/adminhtml_status_renderer_button'));


        return $this;
    }
}