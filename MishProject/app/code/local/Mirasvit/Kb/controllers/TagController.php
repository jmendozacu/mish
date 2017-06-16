<?php
class Mirasvit_Kb_TagController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _initTag()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $tag = Mage::getModel('kb/tag')->load($id);
            if ($tag->getId() > 0) {
                Mage::register('current_tag', $tag);
                return $tag;
            }
        }
    }

    public function viewAction()
    {
        if ($this->_initTag()) {
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->_forward('no_rote');
        }
    }

    /************************/

}