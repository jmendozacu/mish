<?php

class VES_VendorsQuickAddProduct_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
    $rootcatId= Mage::app()->getStore()->getRootCategoryId();
    $categories = Mage::getModel('catalog/category')->getCategories($rootcatId);

    echo array($this, get_categories($categories));
    }

    function get_categories($categories) {
        $array= '<ul>';
        foreach($categories as $category) {
            $cat = Mage::getModel('catalog/category')->load($category->getId());
            $count = $cat->getProductCount();
            $array .= '<li>'.
                '<a href="' . Mage::getUrl($cat->getUrlPath()). '">' .
                $category->getName() . "(".$count.")</a>\n";
            if($category->hasChildren()) {
                $children = Mage::getModel('catalog/category')->getCategories($category->getId());
                $array .=  get_categories($children);
            }
            $array .= '</li>';
        }
        return  $array . '</ul>';
    }
}

