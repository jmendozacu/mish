<?php
class VES_VendorsCategory_Vendor_Catalog_TestController extends VES_Vendors_Controller_Action {
    public function indexAction() {
        var_dump(Mage::helper('vendorscategory')->generateRootNode());
        var_dump(ltrim('/28','/'));
    }

    public function reCursiveSetOrderAction($category) {
        $children = Mage::getModel('vendorscategory/category')->getCollection()
            ->addFieldToFilter('parent_category_id',$category->getId());
        $children->getSelect()->order('main_table.sort_order ASC');

        echo $children->getSelect();

        if($children->count() <= 0) return;
        $i = 1;
        foreach($children as $child) {
            Mage::log('-child'.$child->getId());
            $child->setData('sort_order',$i)->save();
            $this->reCursiveSetOrderAction($child);
            $i++;
        }
    }
}