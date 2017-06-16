<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Inventorybarcode
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorybarcode Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorybarcode
 * @author      Magestore Developer
 */

class Magestore_Inventorybarcode_Block_Adminhtml_Printbarcode_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'inventorybarcode';
        $this->_controller = 'adminhtml_printbarcode';

        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            $this->removeButton('delete');

        } else {
            $this->removeButton('reset');
            $this->removeButton('saveandcontinue');
            $this->removeButton('cancel');
        }
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        $this->_addButton('print_view', array(
                    'label' => Mage::helper('inventorybarcode')->__('Print View'),
                    'onclick' => 'printView()',
                    'class' => 'add',
                        ), 0);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('inventory_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'inventory_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'inventory_content');
            }
            function saveAndContinueEdit(){               
                editForm.submit($('edit_form').action+'back/edit/');
            }
            function printView(){
                var barPerRow=$('barcode_per_row').value;
                var barUnit=$('barcode_unit').value;
                var pageHeight=$('page_height').value;
                var velticalDistantce=$('veltical_distantce').value;
                var horizontalDistance=$('horizontal_distance').value;
                var pageWidth=$('page_width').value;
                var barType=$('barcode_type').value;
                var topMargin=$('top_margin').value;
                var leftMargin=$('left_margin').value;
                var rightMargin=$('right_margin').value;
                var bottomMargin=$('bottom_margin').value;
                var barcodeWidth=$('barcode_width').value;
                var barcodeHeight=$('barcode_height').value;
                var fontSize=$('font_size').value;
                var select1 = $('attribute_show');
                        var wSelected = '';
                        var j = 0;
                        for (var i = 0; i < select1.length; i++) {
                            if (select1.options[i].selected){
                                if(j!=0) wSelected += ',';
                                wSelected += select1.options[i].value;
                                j++;
                            }
                        }
                var attributeShow = wSelected;
                if(!barPerRow){
                    alert('Please enter \"Labels per row\" to print view!');
                    return false;
                }
                if(!pageWidth){
                    alert('Please enter \"Page width\" to print view!');
                    return false;
                }
                if(!pageHeight){
                    alert('Please enter \"Page heigth\" to print view!');
                    return false;
                }
                if(!barcodeWidth){
                    alert('Please enter \"Barcode width\" to print view!');
                    return false;
                }
                if(!barcodeHeight){
                    alert('Please enter \"Barcode height\" to print view!');
                    return false;
                }
                if(!fontSize){
                    alert('Please enter \"Font size\" to print view!');
                    return false;
                }
                var url = '" . $this->getUrl('adminhtml/inb_printbarcode/printView') . "';
                var url = url+'barPerRow/'+barPerRow+'/barUnit/'+barUnit+'/pageHeight/'+pageHeight+'/velticalDistantce/'+velticalDistantce+'/horizontalDistance/'+horizontalDistance+'/pageWidth/'+pageWidth+'/barType/'+barType+'/topMargin/'+topMargin+'/leftMargin/'+leftMargin+'/rightMargin/'+rightMargin+'/bottomMargin/'+bottomMargin+'/attributeShow/'+attributeShow+'/barcodeWidth/'+barcodeWidth+'/barcodeHeight/'+barcodeHeight+'/fontSize/'+fontSize;
                window.open(url ,'_blank', 'scrollbars=yes, resizable=yes, width=750, height=500, left=80, menubar=yes');                
            } 

        ";
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText() {
        if (Mage::registry('barcodeTemplate_data')
            && Mage::registry('barcodeTemplate_data')->getId()
        ) {
            return Mage::helper('inventorybarcode')->__("View Barcode Template '%s'", $this->htmlEscape(Mage::registry('barcodeTemplate_data')->getBarcodeTemplateName())
            );
        }
        return Mage::helper('inventorybarcode')->__('New Barcode Template');
    }

}

