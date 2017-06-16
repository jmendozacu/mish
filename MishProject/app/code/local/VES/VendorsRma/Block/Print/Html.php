<?php

class VES_VendorsRma_Block_Print_Html extends Mage_Core_Block_Template
{
    protected $_urls = array();
    protected $_title = '';

    public function __construct()
    {
        parent::__construct();
        $this->_urls = array(
            'base'      => Mage::getBaseUrl('web'),
            'baseSecure'=> Mage::getBaseUrl('web', true),
            'current'   => $this->getRequest()->getRequestUri()
        );

        $action = Mage::app()->getFrontController()->getAction();
        if ($action) {
            $this->addBodyClass($action->getFullActionName('-'));
        }

        $this->_beforeCacheUrl();
    }

    public function getBaseUrl()
    {
        return $this->_urls['base'];
    }

    public function getBaseSecureUrl()
    {
        return $this->_urls['baseSecure'];
    }

    public function getCurrentUrl()
    {
        return $this->_urls['current'];
    }


    public function getRequestRma(){
        return Mage::registry('request');
    }


    /**
     *  Print Logo URL (Conf -> Sales -> Invoice and Packing Slip Design)
     *
     *  @return	  string
     */
    public function getPrintLogoUrl ()
    {
        $vendor = Mage::getModel("vendors/vendor")->load($this->getRequestRma()->getVendorId());

        $logo = Mage::getBaseUrl('media').$vendor->getLogo();

        return $logo;
    }

    public function getPrintLogoText()
    {
        return Mage::getStoreConfig('sales/identity/address');
    }

    public function setHeaderTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    public function getHeaderTitle()
    {
        return $this->_title;
    }

    /**
     * Add CSS class to page body tag
     *
     * @param string $className
     * @return Mage_Page_Block_Html
     */
    public function addBodyClass($className)
    {
        $className = preg_replace('#[^a-z0-9]+#', '-', strtolower($className));
        $this->setBodyClass($this->getBodyClass() . ' ' . $className);
        return $this;
    }

    public function getLang()
    {
        if (!$this->hasData('lang')) {
            $this->setData('lang', substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2));
        }
        return $this->getData('lang');
    }

    public function setTheme($theme)
    {
        $arr = explode('/', $theme);
        if (isset($arr[1])) {
            Mage::getDesign()->setPackageName($arr[0])->setTheme($arr[1]);
        } else {
            Mage::getDesign()->setTheme($theme);
        }
        return $this;
    }

    public function getBodyClass()
    {
        return $this->_getData('body_class');
    }

    public function getAbsoluteFooter()
    {
        return Mage::getStoreConfig('design/footer/absolute_footer');
    }

    /**
     * Processing block html after rendering
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        return $this->_afterCacheUrl($html);
    }
}
