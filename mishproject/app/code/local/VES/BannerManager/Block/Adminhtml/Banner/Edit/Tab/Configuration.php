<?php

class VES_BannerManager_Block_Adminhtml_Banner_Edit_Tab_Configuration extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('bannermanager_configuration', array('legend'=>Mage::helper('bannermanager')->__('Configuration information')));
   
      $fieldset->addField('template', 'select', array(
          'label'     => Mage::helper('bannermanager')->__('Type'),
          'name'      => 'template',
      	  'required'  => true,
          'values'    => array(
      		  array(
                  'value'     => '0',
                  'label'     => Mage::helper('bannermanager')->__('Prototype Slider'),
              ),

              array(
                  'value'     => '1',
                  'label'     => Mage::helper('bannermanager')->__('Nivo Slider (jQuery)'),
              ),
          ),
      ));
      $disabled = Mage::registry('bannermanager_data')->getTemplate()=='static.phtml'?'disabled':'';

      $fieldset->addField('display_description', 'select', array(
          'label'     => Mage::helper('bannermanager')->__('Display Description'),
          'name'      => 'display_description',
      	  'disabled'  => $disabled,
          'values'    => Mage::getModel('bannermanager/yesno')->getOptionArray(),
      ));
  		/**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }
		
	   $prototype = $form->addFieldset(
            'prototype_fieldset',
            array(
                'legend'=>Mage::helper('bannermanager')->__('Prototype Slider Options'),
            )
        );

        $prototype->addField('controls_type', 'select', array(
            'label'     => Mage::helper('bannermanager')->__('Controls type'),
            'title'     => Mage::helper('bannermanager')->__('Controls type'),
            'name'      => 'controls_type',
            'required'  => true,
            'options'   => array(
                'number' => Mage::helper('bannermanager')->__('Numbers'),
                'arrow' => Mage::helper('bannermanager')->__('Arrows')
            )
        ));

        $prototype->addField('width', 'text', array(
            'name'      => 'width',
            'label'     => Mage::helper('bannermanager')->__('Width'),
            'title'     => Mage::helper('bannermanager')->__('Width'),
            'required'  => true
        ));

        $prototype->addField('height', 'text', array(
            'name'      => 'height',
            'label'     => Mage::helper('bannermanager')->__('Height'),
            'title'     => Mage::helper('bannermanager')->__('Height'),
            'required'  => true
        ));

        $prototype->addField('easing', 'select', array(
            'label'     => Mage::helper('bannermanager')->__('Effect'),
            'title'     => Mage::helper('bannermanager')->__('Effect'),
            'name'      => 'easing',
            'required'  => true,
            'options'   => array(
                'scroll' => Mage::helper('bannermanager')->__('Scroll'),
                'speedscroll' => Mage::helper('bannermanager')->__('Speedscroll'),
                'fade' => Mage::helper('bannermanager')->__('Fade'),
                'blend' => Mage::helper('bannermanager')->__('Blend'),
                'mosaic' => Mage::helper('bannermanager')->__('Mosaic')
            )
        ));

        $prototype->addField('duration', 'text', array(
            'name'      => 'duration',
            'label'     => Mage::helper('bannermanager')->__('Duration'),
            'title'     => Mage::helper('bannermanager')->__('Duration'),
            'required'  => true,
            'note' => Mage::helper('bannermanager')->__('Default : 0.5'),
        ));

        $prototype->addField('frequency', 'text', array(
            'name'      => 'frequency',
            'label'     => Mage::helper('bannermanager')->__('Frequency'),
            'title'     => Mage::helper('bannermanager')->__('Frequency'),
            'required'  => true,
            'note' => Mage::helper('bannermanager')->__('Default : 4'),
        ));

        $prototype->addField('autoglide', 'select', array(
            'label'     => Mage::helper('bannermanager')->__('Autoglide'),
            'title'     => Mage::helper('bannermanager')->__('Autoglide'),
            'name'      => 'autoglide',
            'required'  => true,
            'options'   => array(
                '1' => Mage::helper('bannermanager')->__('Enabled'),
                '0' => Mage::helper('bannermanager')->__('Disabled')
            )
        ));

        $nivo = $form->addFieldset(
            'nivo_fieldset',
            array(
                'legend'=>Mage::helper('bannermanager')->__('Nivo Slider Options'),
            )
        );

        $nivo->addField('theme', 'select', array(
            'label'     => Mage::helper('bannermanager')->__('Theme'),
            'title'     => Mage::helper('bannermanager')->__('Theme'),
            'name'      => 'theme',
            'required'  => true,
            'options'   => array(
                'default' => Mage::helper('bannermanager')->__('Default'),
                'light' => Mage::helper('bannermanager')->__('Light'),
                'dark' => Mage::helper('bannermanager')->__('Dark'),
                'bar' => Mage::helper('bannermanager')->__('Bar'),
            )
        ));

        $nivo->addField('nivoeffect', 'multiselect', array(
            'label'     => Mage::helper('bannermanager')->__('Effect'),
            'title'     => Mage::helper('bannermanager')->__('Effect'),
            'name'      => 'nivoeffect[]',
            'required'  => true,
            'values'    => Mage::helper('bannermanager')->getEffectOptionsData(),

        ));

        $nivo->addField('slices', 'text', array(
            'name'      => 'slices',
            'label'     => Mage::helper('bannermanager')->__('Slices(For slice animations)'),
            'title'     => Mage::helper('bannermanager')->__('Slices(For slice animations)'),
            'required'  => true,
            'note' => Mage::helper('bannermanager')->__('Default : 15'),
        ));

        $nivo->addField('boxCols', 'text', array(
            'name'      => 'boxCols',
            'label'     => Mage::helper('bannermanager')->__('Box Cols(For box animations)'),
            'title'     => Mage::helper('bannermanager')->__('Box Cols(For box animations)'),
            'required'  => true,
            'note' => Mage::helper('bannermanager')->__('Default : 8'),
        ));

        $nivo->addField('boxRows', 'text', array(
            'name'      => 'boxRows',
            'label'     => Mage::helper('bannermanager')->__('Box Rows(For box animations)'),
            'title'     => Mage::helper('bannermanager')->__('Box Rows(For box animations)'),
            'required'  => true,
            'note' => Mage::helper('bannermanager')->__('Default : 4'),
        ));

        $nivo->addField('animSpeed', 'text', array(
            'name'      => 'animSpeed',
            'label'     => Mage::helper('bannermanager')->__('Slide transition speed'),
            'title'     => Mage::helper('bannermanager')->__('Slide transition speed'),
            'required'  => true,
            'note' => Mage::helper('bannermanager')->__('Default : 500'),
        ));

        $nivo->addField('pauseTime', 'text', array(
            'name'      => 'pauseTime',
            'label'     => Mage::helper('bannermanager')->__('How long each slide will show'),
            'title'     => Mage::helper('bannermanager')->__('How long each slide will show'),
            'required'  => true,
            'note' => Mage::helper('bannermanager')->__('Default : 3000'),
        ));

        $nivo->addField('directionNav', 'select', array(
            'label'     => Mage::helper('bannermanager')->__('Next & Prev navigation'),
            'title'     => Mage::helper('bannermanager')->__('Next & Prev navigation'),
            'name'      => 'directionNav',
            'required'  => true,
            'options'   => array(
                'true' => Mage::helper('bannermanager')->__('Yes'),
                'false' => Mage::helper('bannermanager')->__('No')
            )
        ));
        $nivo->addField('controlNav', 'select', array(
            'label'     => Mage::helper('bannermanager')->__('Use thumbnails for Control Nav'),
            'title'     => Mage::helper('bannermanager')->__('Use thumbnails for Control Nav'),
            'name'      => 'controlNav',
            'required'  => true,
            'options'   => array(
                'true' => Mage::helper('bannermanager')->__('Yes'),
                'false' => Mage::helper('bannermanager')->__('No')
            )
        ));

        $nivo->addField('pauseOnHover', 'select', array(
            'label'     => Mage::helper('bannermanager')->__('Stop animation while hovering'),
            'title'     => Mage::helper('bannermanager')->__('Stop animation while hovering'),
            'name'      => 'pauseOnHover',
            'required'  => true,
            'options'   => array(
                'true' => Mage::helper('bannermanager')->__('Yes'),
                'false' => Mage::helper('bannermanager')->__('No')
            )
        ));

        $nivo->addField('manualAdvance', 'select', array(
            'label'     => Mage::helper('bannermanager')->__('Autoglide'),
            'title'     => Mage::helper('bannermanager')->__('Autoglide'),
            'name'      => 'manualAdvance',
            'required'  => true,
            'options'   => array(
                'false' => Mage::helper('bannermanager')->__('Yes'),
                'true' => Mage::helper('bannermanager')->__('No')
            )
        ));

        $form->setValues(array_merge(
            array(
                'duration'  => '0.5',
                'frequency' => '4.0',
                'slices'    => '15',
                'boxCols'   => '8',
                'boxRows'   => '4',
                'animSpeed' => '500',
                'pauseTime' => '3000'
            ),
            Mage::registry('bannermanager_data')->getData()
        ));

		
		
		
		
      if ( Mage::getSingleton('adminhtml/session')->getBannerManagerData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getBannerManagerData());
          Mage::getSingleton('adminhtml/session')->setBannerManagerData(null);
      } elseif ( Mage::registry('bannermanager_data') ) {
          //$form->setValues(Mage::registry('bannermanager_data')->getData());
       	if(Mage::registry('bannermanager_data')->getId())
      	if (!Mage::app()->isSingleStoreMode()) {
				$resource = Mage::getSingleton('core/resource');
		     
		    	$readConnection = $resource->getConnection('core_read');
		     
		    	$query = 'SELECT store_id FROM ' . $resource->getTableName('bannermanager/banner_store') .' WHERE banner_id='.Mage::registry('bannermanager_data')->getId();

		    	$arrStoreId = $readConnection->fetchCol($query);

			  // set value for store view selected:
			  $form->getElement('store_id')->setValue($arrStoreId);
          }
      }
      return parent::_prepareForm();
  }
}