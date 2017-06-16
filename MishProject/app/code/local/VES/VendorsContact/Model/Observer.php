<?php
class VES_VendorsContact_Model_Observer
{

    public function ves_vendorspage_contact_prepare(Varien_Event_Observer $observer){
        $profileBlock = $observer->getProfileBlock();
        $ratingBlock = $profileBlock->getLayout()->createBlock('vendorscontact/contact_sidebar','vendor.contact')->setTemplate('ves_vendorscontact/profile/sidebar.phtml');
        $footerProfile = $profileBlock->getChild('footer_profile');
        $footerProfile->insert($ratingBlock, '', false, 'vendors_contact_block');
    }

}