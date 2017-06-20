<?php
/**
 * Created by PhpStorm.
 * User: namnh_000
 * Date: 4/15/14
 * Time: 12:09 PM
 */

class VES_VendorsLiveChat_Block_Vendor_Box_Geo extends VES_VendorsLiveChat_Block_Vendor_Box
{
        public function getContentCmsBlock(){
            return Mage::helper("vendorslivechat")->getStaticBlock();
        }
}