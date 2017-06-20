<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * MageWorx DeliveryZone extension
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_DeliveryZone_Block_Adminhtml_Rates_Import_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm() {
        $form = new Varien_Data_Form(array(
                        'id'      => 'edit_form',
                        'action'  => $this->getUrl('*/*/importRatesSave', array('id' => $this->getRequest()->getParam('id'))),
                        'method'  => 'post',
                        'enctype' => 'multipart/form-data'
                )
        );
        $form->setUseContainer(true);
        $fieldset = $form->addFieldset('import_form', array('legend' => Mage::helper('deliveryzone')->__('Import Settings')));
        $fieldset->addField('import_file', 'file', array(
            'label'     => Mage::helper('deliveryzone')->__('Import File'),
            'required'  => false,
            'name'      => 'import_file',
//            'note'   => $this->__("<a href='%s' target='_blank'>",$this->getSampleUrl()) . $this->__("Download") ."</a> ". $this->__("empty file"),
        ));
        $this->setForm($form);
        return parent::_prepareForm();
    }
    
    /**
     * DEPRICATED
     * @return string url
     */
//    public function getSampleUrl() {
//        return $this->getUrl("*/*/emptyExportFile", array('_current'=>true));
//    }
}
