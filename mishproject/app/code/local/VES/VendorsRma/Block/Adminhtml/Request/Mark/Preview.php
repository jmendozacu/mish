<?php

class VES_VendorsRma_Block_Adminhtml_Request_Mark_Preview extends Mage_Adminhtml_Block_Widget
{
    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        // Start store emulation process
        // Since the Transactional Email preview process has no mechanism for selecting a store view to use for
        // previewing, use the default store view
        $defaultStoreId = Mage::app()->getDefaultStoreView()->getId();
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($defaultStoreId);

        /** @var $template Mage_Core_Model_Email_Template */
        $template = Mage::getModel('core/email_template');
        $id = (int)$this->getRequest()->getParam('id');

        $content = Mage::getSingleton('vendorsrma/mestemplate')->load($id);
        
        $template->setTemplateText($content->getData("content_template"));
        
        /* @var $filter Mage_Core_Model_Input_Filter_MaliciousCode */
        $filter = Mage::getSingleton('core/input_filter_maliciousCode');

        $requestId = (int)$this->getRequest()->getParam('request');
        $request = Mage::getSingleton('vendorsrma/request')->load($requestId);

        $template->setTemplateText(
            $filter->filter($template->getTemplateText())
        );

        Varien_Profiler::start("email_template_proccessing");
        $vars = array("request"=>$request);

        $templateProcessed = $template->getProcessedTemplate($vars, true);

        if ($template->isPlain()) {
            $templateProcessed = "<pre>" . htmlspecialchars($templateProcessed) . "</pre>";
        }

        Varien_Profiler::stop("email_template_proccessing");

        // Stop store emulation process
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $templateProcessed;
    }
}
