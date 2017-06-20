<?php
class Mirasvit_Kb_Adminhtml_ArticleController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction ()
    {
        $this->loadLayout()->_setActiveMenu('kb');

        return $this;
    }

    public function indexAction ()
    {
        $this->_title($this->__('Articles'));
        $this->_initAction();
        $this->_addContent($this->getLayout()
            ->createBlock('kb/adminhtml_article'));
        $this->renderLayout();
    }

    public function addAction ()
    {
        $this->_title($this->__('New Article'));

        $this->_initModel();

        $this->_initAction();
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Article  Manager'),
                Mage::helper('adminhtml')->__('Article Manager'), $this->getUrl('*/*/'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add Article '), Mage::helper('adminhtml')->__('Add Article'));

        $this->getLayout()
            ->getBlock('head')
            ->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('kb/adminhtml_article_edit'))
                ->_addLeft($this->getLayout()->createBlock('kb/adminhtml_article_edit_tabs'));
        $this->renderLayout();
    }

    public function editAction ()
    {
        $model = $this->_initModel();

        if ($model->getId()) {
            $this->_title($this->__("Edit Article '%s'", $model->getName()));
            $this->_initAction();
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Articles'),
                    Mage::helper('adminhtml')->__('Articles'), $this->getUrl('*/*/'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit Article '),
                    Mage::helper('adminhtml')->__('Edit Article '));

            $this->getLayout()
                ->getBlock('head')
                ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('kb/adminhtml_article_edit'))
                    ->_addLeft($this->getLayout()->createBlock('kb/adminhtml_article_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('The article does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction ()
    {
        if ($data = $this->getRequest()->getPost()) {

            $model = $this->_initModel();
            $data['category_ids'] = array_unique(explode(',', trim($data['category_ids'], ',')));


            $model->addData($data);
            Mage::helper('kb/tag')->setTags($model, $data['tags']);
            Mage::helper('kb')->setRating($model);

            //format date to standart
            // $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            // Mage::helper('mstcore/date')->formatDateForSave($model, 'active_from', $format);
            // Mage::helper('mstcore/date')->formatDateForSave($model, 'active_to', $format);

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Article was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find article to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction ()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('kb/article');

                $model->setId($this->getRequest()
                    ->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Article was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()
                    ->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function categoriesJsonAction()
    {
        $this->_initModel();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('kb/adminhtml_article_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('article_id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select article(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('kb/article')
                        ->setIsMassDelete(true)
                        ->load($id);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($ids)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function _initModel()
    {
        $model = Mage::getModel('kb/article');
        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        Mage::register('current_article', $model);

        return $model;
    }
}