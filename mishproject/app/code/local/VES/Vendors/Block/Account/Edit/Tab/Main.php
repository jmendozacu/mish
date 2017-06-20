<?php

class VES_Vendors_Block_Account_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        Mage::dispatchEvent('ves_vendors_account_edit_tab_main_before', array('tab' => $this, 'form' => $form));

        $fieldset = $form->addFieldset('vendors_form', array('legend' => Mage::helper('vendors')->__('Vendor information')));

        $fieldset->addField('vendor_id', 'label', array(
            'label' => Mage::helper('vendors')->__('Vendor Id'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'vendor_id',
        ));

        $fieldset->addField('group_id', 'label', array(
            'label' => Mage::helper('vendors')->__('Group'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'group_id',
        ));
        $fieldset->addField('email', Mage::app()->getStore()->isAdmin() ? 'text' : 'label', array(
            'label' => Mage::helper('vendors')->__('Email'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'email',
        ));
        $fieldset->addField('firstname', 'text', array(
            'label' => Mage::helper('vendors')->__('First Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'firstname',
        ));
        $fieldset->addField('lastname', 'text', array(
            'label' => Mage::helper('vendors')->__('Last Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'lastname',
        ));
        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('vendors')->__('Display Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));
        $fieldset->addField('logo', 'image', array(
            'label' => Mage::helper('vendors')->__('Logo'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'logo',
        ));
        $fieldset->addField('company', 'text', array(
            'label' => Mage::helper('vendors')->__('Company'),
            'name' => 'company',
        ));

        /* start here */
        $fieldset->addField('passport', 'text', array(
            'label' => Mage::helper('vendors')->__('passport'),
            'name' => 'passport',
        ));
        $fieldset->addField('birthday', 'text', array(
            'label' => Mage::helper('vendors')->__('birthday'),
            'name' => 'birthday',
        ));
         $fieldset->addField('sex', 'text', array(
            'label' => Mage::helper('vendors')->__('sex'),
            'name' => 'sex',
        ));
          $fieldset->addField('Facebook', 'text', array(
            'label' => Mage::helper('vendors')->__('Facebook'),
            'name' => 'Facebook',
        ));
           $fieldset->addField('roomaddress', 'text', array(
            'label' => Mage::helper('vendors')->__('roomaddress'),
            'name' => 'roomaddress',
        ));

           /* end */
       
        
        $fieldset->addField('telephone', 'text', array(
            'label' => Mage::helper('vendors')->__('Telephone'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'telephone',
        ));
        $fieldset->addField('address', 'text', array(
            'label' => Mage::helper('vendors')->__('Address'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'address',
        ));
        $fieldset->addField('city', 'text', array(
            'label' => Mage::helper('vendors')->__('City'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'city',
        ));
        $countryOptions = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
        $cOptions = array();
        foreach ($countryOptions as $option) {
            $cOptions[$option['value']] = $option['label'];
        }
        $fieldset->addField('country_id', 'select', array(
            'label' => Mage::helper('vendors')->__('Country'),
            'name' => 'country_id',
            'options' => $cOptions,
        ));
        $fieldset->addField('region', 'text', array(
            'label' => Mage::helper('vendors')->__('State/Province'),
            'required' => false,
            'name' => 'region',
        ));

        $fieldset->addField('region_id', 'select', array(
            'label' => Mage::helper('vendors')->__('State/Province'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'region_id',
        ));

        $fieldset->addField('postcode', 'text', array(
            'label' => Mage::helper('vendors')->__('Zip/Postal Code'),
            'name' => 'postcode',
        ));

        $additionalAttributes = $this->getAdditionalAttributes();
        if ($additionalAttributes->count()) {
            foreach ($additionalAttributes as $attribute) {
                $inputType = $attribute->getFrontend()->getInputType();
                if (in_array($inputType, array('multiselect', 'select'))) {
                    $attribute->setSourceModel('eav/entity_attribute_source_table');
                }
            }
            $fieldset2 = $this->getForm()->addFieldset('additional_info', array('legend' => Mage::helper('vendors')->__('Additional Information')));
            $this->_setFieldset($additionalAttributes, $fieldset2);
        }

        $fieldset1 = $this->getForm()->addFieldset('password_management', array('legend' => Mage::helper('vendors')->__('Password Management')));
        $fieldset1->addField('new_password', 'password', array(
            'label' => Mage::registry('vendors_data')->getId() ? Mage::helper('vendors')->__('New Password') : Mage::helper('vendors')->__('Password'),
            'name' => 'new_password',
        ));

        $regionElement = $form->getElement('region');
        $regionElement->setRequired(true);
        if ($regionElement) {
            $regionElement->setRenderer(Mage::getModel('vendors/vendor_renderer_region'));
        }

        $regionElement = $form->getElement('region_id');
        if ($regionElement) {
            $regionElement->setNoDisplay(true);
        }
        if (Mage::getSingleton('adminhtml/session')->getVendorsData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getVendorsData());
            Mage::getSingleton('adminhtml/session')->setVendorsData(null);
        } elseif (Mage::registry('vendors_data')) {
            $data = Mage::registry('vendors_data')->getData();
            $data['group_id'] = Mage::registry('vendors_data')->getGroup()->getName();
            $form->setValues($data);
        }

        Mage::dispatchEvent('ves_vendors_account_edit_tab_main_after', array('tab' => $this, 'form' => $form));


        return parent::_prepareForm();
    }

    /**
     * Retrieve predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes() {
        return array('ves_file' => 'VES_Vendors_Block_Form_Element_File');
    }

    /**
     * Set Fieldset to Form
     *
     * @param array $attributes attributes that are to be added
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param array $exclude attributes that should be skipped
     */
    protected function _setFieldset($attributes, $fieldset, $exclude = array()) {
        $this->_addElementTypes($fieldset);
        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            if (!$attribute || ($attribute->hasIsVisible() && !$attribute->getIsVisible())) {
                continue;
            }
            if (($inputType = $attribute->getFrontend()->getInputType()) && !in_array($attribute->getAttributeCode(), $exclude) && ('media_image' != $inputType)) {

                $fieldType = $inputType;
                if ($fieldType == 'boolean') {
                    $fieldType = 'select';
                } elseif ($fieldType == 'file') {
                    $fieldType = 'ves_file';
                }

                $rendererClass = $attribute->getFrontend()->getInputRendererClass();
                if (!empty($rendererClass)) {
                    $fieldType = $inputType . '_' . $attribute->getAttributeCode();
                    $fieldset->addType($fieldType, $rendererClass);
                }

                if ($attribute->getAttributeCode() == 'seller_with_invoice') {
                    $element = $fieldset->addField($attribute->getAttributeCode(), $fieldType, array(
                                'name' => $attribute->getAttributeCode(),
                                'label' => $attribute->getFrontend()->getLabel(),
                                'class' => $attribute->getFrontend()->getClass(),
                                'required' => $attribute->getIsRequired(),
                                'note' => $attribute->getNote(),
                                'onchange' => 'showHideElement(this.value)',
                                'container_id' => $attribute->getAttributeCode() . '_fuckyou',
                                    )
                            )
                            ->setEntityAttribute($attribute);
                } else {
                    $element = $fieldset->addField($attribute->getAttributeCode(), $fieldType, array(
                                'name' => $attribute->getAttributeCode(),
                                'label' => $attribute->getFrontend()->getLabel(),
                                'class' => $attribute->getFrontend()->getClass(),
                                'required' => $attribute->getIsRequired(),
                                'note' => $attribute->getNote(),
                                'container_id' => $attribute->getAttributeCode() . '_fuckyou',
                                    )
                            )
                            ->setEntityAttribute($attribute);
                }

                $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));

                if ($inputType == 'select') {
                    $element->setValues($attribute->getSource()->getAllOptions(true, true));
                } else if ($inputType == 'multiselect') {
                    $element->setValues($attribute->getSource()->getAllOptions(false, true));
                    $element->setCanBeEmpty(true);
                } else if ($inputType == 'date') {
                    $element->setImage($this->getSkinUrl('images/grid-cal.gif'));
                    $element->setFormat(Mage::app()->getLocale()->getDateFormatWithLongYear());
                } else if ($inputType == 'datetime') {
                    $element->setImage($this->getSkinUrl('images/grid-cal.gif'));
                    $element->setTime(true);
                    $element->setStyle('width:50%;');
                    $element->setFormat(
                            Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
                    );
                } else if ($inputType == 'multiline') {
                    $element->setLineCount($attribute->getMultilineCount());
                } elseif ($inputType == 'boolean') {
                    $values = array(
                        array('label' => Mage::helper('vendors')->__('No'), 'value' => 0),
                        array('label' => Mage::helper('vendors')->__('Yes'), 'value' => 1),
                    );
                    $element->setValues($values);
                }
            }
        }

        $element->setAfterElementHtml('<script>
        //<![CDATA[
        showHideElement('.$this->getSellerInvoice().');
        function showHideElement(obj) {        
            if (obj == 1) {
                $("rut_of_seller_fuckyou").hide();
                $("name_legal_company_fuckyou").show();
                $("rut_legal_company_fuckyou").show();
                $("name_legal_representative_fuckyou").show();
                $("rut_legal_representative_fuckyou").show();
                //$("company_fuckyou").show();                              
            } else {
                $("rut_of_seller_fuckyou").show();
                $("name_legal_company_fuckyou").hide();
                $("rut_legal_company_fuckyou").hide();
                $("name_legal_representative_fuckyou").hide();
                $("rut_legal_representative_fuckyou").hide();
                //$("company_fuckyou").hide();                
            }
        }
        //]]>
        
        </script>');
    }

    /**
     * Get All additional attributes
     * 
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function getAdditionalAttributes() {
        $vendorAttributeType = Mage::getResourceModel('eav/entity_type_collection')->addFieldToFilter('entity_type_code', 'ves_vendor')->getFirstItem();
        $collection = Mage::getResourceModel('eav/entity_attribute_collection')
                ->addFieldToFilter('entity_type_id', $vendorAttributeType->getId())
                ->addFieldToFilter('is_user_defined', true)
                ->setOrder('sorting_order', 'ASC');
        return $collection;
    }

    public function getSellerInvoice() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('vendors/vendor')->load($id);
        return $model->getSellerWithInvoice();
    }

}