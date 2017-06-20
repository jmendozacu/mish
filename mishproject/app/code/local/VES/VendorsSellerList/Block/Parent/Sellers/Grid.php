<?php
class VES_VendorsSellerList_Block_Parent_Sellers_Grid extends VES_VendorsSellerList_Block_Parent_Sellers {
    protected $_columnLayoutDepend = array();
    protected $_defaultColumn = 5;

    public function _construct() {
        parent::_construct();
        $collection = $this->getAllSellers(false);
        $this->setCollection($collection);
    }

    protected function _prepareLayout() {
        parent::_prepareLayout();
        $this->setTemplate('ves_vendorssellerlist/parent/sellers/grid.phtml');

        if($this->isViewAll()) {
            $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager')->setTemplate('ves_vendorssellerlist/parent/sellers/pager.phtml');
            $pager->setAvailableLimit($this->getAvailableLimit());
            $pager->setCollection($this->getCollection());
            $this->setChild('pager', $pager);
        }
        $this->getCollection()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getColumnCount() {
        if (!$this->_getData('column_number')) {
            $pageLayoutCode = $this->getPageLayoutCode();
            if ($pageLayoutCode && $this->getColumnLayoutDepend($pageLayoutCode)) {
                $this->setData(
                    'column_number',
                    $this->getColumnLayoutDepend($pageLayoutCode)
                );
            } else {
                $this->setData('column_number', $this->_defaultColumn);
            }
        }
        return (int) $this->_getData('column_number');
    }

    /**
     * resize image logo(optional)
     * @param $imagePath
     * @param int $height
     * @param int $width
     * @return string
     */
    public function resize($imagePath,$height=140, $width = 140) {
        $imageData = explode('/', $imagePath);
        $imageFileName = end($imageData);

        $upload_dir = Mage::getBaseDir('media') . DS."ves_vendors".DS."sellers".DS ;
        $cache_dir = $upload_dir.DS.'cache'.DS;

        $cacheUrl = Mage::getUrl('media').'ves_vendors/sellers/cache/';

        if(file_exists($cache_dir.$imageFileName)) return $cacheUrl.$imageFileName;
        elseif (file_exists(Mage::getBaseDir('media').DS.$imagePath)) {
            if (!is_dir($cache_dir)) {
                mkdir($cache_dir);
            }

            $image = new Varien_Image(Mage::getBaseDir('media').DS.$imagePath);
            $image->constrainOnly(true);
            $image->keepAspectRatio(true);
            $image->keepFrame(true);
            $image->keepTransparency(true);
            $image->resize($height, $width);
            $image->save($cache_dir.$imageFileName);
        }

        return $cacheUrl.$imageFileName;
    }

    public function getAvailableLimit() {
        $perPageConfigKey = 'vendors/sellers_list/grid_per_page_values';
        $perPageValues = (string)Mage::getStoreConfig($perPageConfigKey);
        $perPageValues = explode(',', $perPageValues);
        $perPageValues = array_combine($perPageValues, $perPageValues);

        return $perPageValues;
    }

    public function addColumnLayoutDepend($pageLayout, $columnCount)
    {
        $this->_columnLayoutDepend[$pageLayout] = $columnCount;
        return $this;
    }

    public function removeColumnLayoutDepend($pageLayout)
    {
        if (isset($this->_columnLayoutDepend[$pageLayout])) {
            unset($this->_columnLayoutDepend[$pageLayout]);
        }

        return $this;
    }

    public function getColumnLayoutDepend($pageLayout)
    {
        if (isset($this->_columnLayoutDepend[$pageLayout])) {
            return $this->_columnLayoutDepend[$pageLayout];
        }

        return false;
    }
}