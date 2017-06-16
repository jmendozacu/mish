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
class MageWorx_DeliveryZone_Block_Rewrite_Checkout_Onepage_Link extends MageWorx_DeliveryZone_Block_Rewrite_Checkout_Onepage_Link_Amasty_Pure
{
    public function isDisabled()
    {
        if(parent::isDisabled()) {
            return true;
        }
        if(Mage::registry('shippingsuite_checkout_disable')) {
            return true;
        }
        return false;
    }
    
    protected function _toHtml() {
        $html = parent::_toHtml();
        if($this->isDisabled()) {
            $html .="<script type='text/javascript'>
                $$('.btn-proceed-checkout').each(function(el) {
                    el.disabled = true;
                    el.addClassName('no-checkout');
                })
                $$('.top-link-checkout').each(function(el) {
                    el.setAttribute('onclick','return false;');
                })
            </script>
            ";
        }
        return $html;
    }
}
