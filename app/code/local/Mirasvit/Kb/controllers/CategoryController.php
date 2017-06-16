<?php
class Mirasvit_Kb_CategoryController extends Mage_Core_Controller_Front_Action
{
    // public function indexAction()
    // {
    //     $this->_forward('*/*/view');
    //     // $this->loadLayout();
    //     // $this->renderLayout();
    // }

    protected function _initCategory()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $category = Mage::getModel('kb/category')->load($id);
            if ($category->getId() > 0) {
                Mage::register('current_kbcategory', $category);
                return $category;
            }
        }
    }

    public function viewAction()
    {
        if ($this->_initCategory()) {
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->_forward('no_rote');
        }
    }

    /************************/

}