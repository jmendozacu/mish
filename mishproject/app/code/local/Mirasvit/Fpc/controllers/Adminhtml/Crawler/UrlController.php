<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Full Page Cache
 * @version   1.0.1
 * @revision  181
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Fpc_Adminhtml_Crawler_UrlController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system')
            ->_addBreadcrumb(Mage::helper('fpc')->__('Full Page Cache'), Mage::helper('fpc')->__('Full Page Cache'));

        return $this;
    }

    public function indexAction ()
    {
        Mage::getSingleton('adminhtml/session')->addNotice($this->_getCronInfo());

        $this->_title($this->__('Crawler URLs'));

        $this->_initAction();

        $this->_addContent($this->getLayout()->createBlock('fpc/adminhtml_crawler_url'));

        $this->renderLayout();
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('url_id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select urls(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('fpc/crawler_url')
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

    protected function _getCronInfo()
    {
        $html = array();

        $cron = Mage::getModel('cron/schedule')->getCollection()
            ->setOrder('executed_at', 'desc')
            ->getFirstItem();

        $last = '-';
        if ($cron->getExecutedAt()) {
            $last = Mage::getSingleton('core/date')->date('d.m.Y H:i', strtotime($cron->getExecutedAt()));
        }
        $html[] = __('Last cron run time: <b>%s</b>', $last);

        $cron = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToFilter('job_code', 'fpc_crawler')
            ->setOrder('executed_at', 'desc')
            ->getFirstItem();
        $last = '-';
        if ($cron->getExecutedAt()) {
            $last = Mage::getSingleton('core/date')->date('d.m.Y H:i', strtotime($cron->getExecutedAt()));
        }
        $html[] = __('Last crawler job run time: <b>%s</b>', $last);

        $cron = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToFilter('job_code', 'fpc_log_import')
            ->setOrder('executed_at', 'desc')
            ->getFirstItem();
        $last = '-';
        if ($cron->getExecutedAt()) {
            $last = Mage::getSingleton('core/date')->date('d.m.Y H:i', strtotime($cron->getExecutedAt()));
        }
        $html[] = __('Last URLs import run time: <b>%s</b>', $last);

        return implode('<br>', $html);
    }
}

