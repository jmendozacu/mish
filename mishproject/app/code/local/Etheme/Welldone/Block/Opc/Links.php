<?php
/**
 * Created by JetBrains PhpStorm.
 * User: admin
 * Date: 31.03.15
 * Time: 3:16
 * To change this template use File | Settings | File Templates.
 */ 
class Etheme_Welldone_Block_Opc_Links extends IWD_Opc_Block_Links {
    /**
     * Add link on checkout page to parent block
     *
     * @return Mage_Checkout_Block_Links
     */
    public function addCheckoutLink(){



        $parentBlock = $this->getParentBlock();
        $text = $this->__('Checkout');
        if ($parentBlock = $this->getParentBlock()) {
            $text = $this->__('Checkout');
            $parentBlock->addLink($text, 'checkout', $text, true, array(), 60, null, 'class="top-link-checkout"');
        }
        return $this;

    }
}