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

class MageWorx_Deliveryzone_Block_Adminhtml_Manager_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
            $form = new Varien_Data_Form(array(
                            'id'      => 'edit_form',
                            'action'  => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                            'method'  => 'post',
                            'enctype' => 'multipart/form-data'
                    )
            );
            $form->setUseContainer(true);
            $this->setForm($form);
            return parent::_prepareForm();
    }
    public function _toHtml() {
        $html = parent::_toHtml();
        $products = join(',',Mage::registry('selected_products'));
        $html = str_replace('</form>', "<input type='hidden' name='product_ids' id='product_ids' value='$products'></form>", $html);
        return $html;
    }
}