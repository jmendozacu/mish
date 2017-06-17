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

class MageWorx_DeliveryZone_Model_System_Adminhtml_Carrier extends Varien_Object
{
    /**
     * Get all carrier methods
     * @return array
     */
    public function toOptionArray() {
        $shipping     = Mage::getSingleton('shipping/config');
        $configFields = Mage::getSingleton('adminhtml/config');
        $section      = 'carriers';
        $sections     = $configFields->getSections($section);
        $section      = $sections->$section->groups;
        $carriers     = $shipping->getAllCarriers();
        ksort($carriers);
        $list = array();
        foreach ($carriers as $_code => $carrier) {
            $title = Mage::getStoreConfig("carriers/$_code/title");
            $_methods = array();
            $options = array();
            if($_methods = $carrier->getAllowedMethods())
            {
                foreach($_methods as $_mcode => $_method)
                {
                    if($_method) {
                        $options[] = array('value' => $_code . '_' . $_mcode, 'label' => '    '.$_method);
                    }
                }
            }
            $list[$_code] = array('label'=>$title,'value'=>$options);
        }
        return $list;
    }
}
