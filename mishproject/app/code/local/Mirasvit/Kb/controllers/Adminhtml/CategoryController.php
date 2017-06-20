<?php
class Mirasvit_Kb_Adminhtml_CategoryController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction ()
    {
        $this->loadLayout()->_setActiveMenu('kb');

        return $this;
    }

    public function indexAction ()
    {
        $this->_title($this->__('Categories'));

        //$this->_initAction();
        $this->_initModel();

        $this->loadLayout()
            ->renderLayout();
    }

    public function addAction ()
    {
        $this->_forward('edit');
    }

    public function editAction ()
    {
        $model = $this->_initModel();

        if ($this->getRequest()->getQuery('isAjax')) {
            $this->loadLayout();

            $breadcrumbsPath = $model->getPath();

            if (empty($breadcrumbsPath)) {
                $breadcrumbsPath = Mage::getSingleton('admin/session')->getDeletedPath(true);
                if (!empty($breadcrumbsPath)) {
                    $breadcrumbsPath = explode('/', $breadcrumbsPath);
                    if (count($breadcrumbsPath) <= 1) {
                        $breadcrumbsPath = '';
                    }
                    else {
                        array_pop($breadcrumbsPath);
                        $breadcrumbsPath = implode('/', $breadcrumbsPath);
                    }
                }
            }
            $editHtml              = $this->getLayout()->createBlock('kb/adminhtml_category_edit')->getFormHtml();
            $breadcrumbsJavascript = $this->getLayout()->createBlock('kb/adminhtml_category_tree')
                ->getBreadcrumbsJavascript($breadcrumbsPath, 'editingItemBreadcrumbs');

            $eventResponse = new Varien_Object(array(
                'content'  => $editHtml.$breadcrumbsJavascript,
                'messages' => $this->getLayout()->getMessagesBlock()->getGroupedHtml(),
            ));

            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode($eventResponse->getData())
            );
        }
    }

    public function saveAction ()
    {
        $model = $this->_initModel();
        $data  = $this->getRequest()->getParams();

        $model->addData($data);

        try {
            $model->save();
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $url = $this->getUrl('*/*/edit', array('_current' => true, 'id' => $model->getId()));
        $this->getResponse()->setBody(
            '<script type="text/javascript">parent.updateContent("' . $url . '", {}, true);</script>'
        );
    }

    public function deleteAction()
    {
        $model = $this->_initModel();

        try {
            $model->delete();
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

            $url = $this->getUrl('*/*/edit', array('_current' => true, 'id' => $model->getId()));
            $this->getResponse()->setBody(
                '<script type="text/javascript">parent.updateContent("' . $url . '", {}, true);</script>'
            );
        }
    }

    public function moveAction()
    {
        $model = $this->_initModel();

        if (!$model) {
            $this->getResponse()->setBody(Mage::helper('kb')->__('Category move error'));
            return;
        }

        $parentNodeId = $this->getRequest()->getPost('pid', false);
        $prevNodeId   = $this->getRequest()->getPost('aid', false);

        if (substr($parentNodeId, 0, 4) == 'category') {
            $parentNodeId = null;
        }

        try {
            $model->save();
            $model->move($parentNodeId, $prevNodeId);
            $this->getResponse()->setBody("SUCCESS");
        } catch (Exception $e){
            $this->getResponse()->setBody($e->getMessage());
        }
    }

    public function categoriesJsonAction()
    {
        if ($categoryId = (int)$this->getRequest()->getParam('id')) {
            $this->getRequest()->setParam('id', $categoryId);

            if (!$category = $this->_initModel()) {
                return;
            }

            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('kb/adminhtml_category_tree')
                    ->getTreeJson($category)
            );
        }
    }

    public function _initModel()
    {
        $model = Mage::getModel('kb/category');
        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        Mage::register('current_model', $model);

        return $model;
    }
}