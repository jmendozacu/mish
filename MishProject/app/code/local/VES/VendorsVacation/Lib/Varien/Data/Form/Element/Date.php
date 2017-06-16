<?php
class VES_VendorsVacation_Lib_Varien_Data_Form_Element_Date extends Varien_Data_Form_Element_Date {
    public function getFormat() {
        return 'MM/d/yyyy';
    }

    public function getImage() {
        return Mage::getDesign()->getSkinUrl('images/grid-cal.gif',
            array(
                '_area' => 'adminhtml',
            )
        );
    }
}