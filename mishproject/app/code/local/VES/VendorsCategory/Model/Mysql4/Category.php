<?php

class VES_VendorsCategory_Model_Mysql4_Category extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {    
        $this->_init('vendorscategory/category', 'category_id');
    }
    
    /**
     * Process URL Rewrite
     */
    public function processUrlRewrite(){
    	$baseUrlKey = Mage::getStoreConfig('vendors/vendor_page/url_key');
    	$vendors = array();
    	$suffix 	= Mage::getStoreConfig('catalog/seo/category_url_suffix');
    	foreach(Mage::getModel('vendorscategory/category')->getCollection() as $category){
    		$vendorId 	= $category->getVendorId();
	    	if(!isset($vendors[$vendorId])){
	    		$vendors[$vendorId] = Mage::getModel('vendors/vendor')->load($vendorId);
	    	}
	    	$vendor 	= $vendors[$vendorId];
	    	$vendorId 	= $vendor->getVendorId();
	    	$urlKey		= $category->getUrlKey();
	    	$idPath 	= $vendorId.'/category/'.$category->getId();
	    	
	    	$requestPath 	= $baseUrlKey.'/'.$vendorId.'/'.$urlKey.$suffix;
	    	$targetPath 	= $baseUrlKey.'/'.$vendorId.'/category/view/id/'.$category->getId();
	    	
	    	if(!$baseUrlKey){
	    		$requestPath 	= $vendorId.'/'.$category->getUrlKey().$suffix;
	    		$targetPath 	= $vendorId.'/category/view/id/'.$category->getId();
	    	}
	    	foreach(Mage::app()->getWebsite($vendor->getWebsiteId())->getStoreIds() as $storeId){
				$urlRewriteCollection = Mage::getModel('core/url_rewrite')->getCollection()
				->addFieldToFilter('id_path',$idPath)
				->addFieldToFilter('store_id',$storeId)
				;
	    		$urlRewriteId = '';
				if($urlRewriteCollection->count()) 
				{
					$urlRewriteId = $urlRewriteCollection->getFirstItem()->getId();
				}
	    		/*check request path is exist or not*/
				$existUrlRewriteObj	= Mage::getModel('core/url_rewrite')->getCollection()
					->addFieldToFilter('request_path', $requestPath)
					->addFieldToFilter('store_id', $storeId)
					;
					
				if($existUrlRewriteObj->count() && $existUrlRewriteObj->getFirstItem()->getId() != $urlRewriteId){
					$requestPath = $baseUrlKey.'/'.$vendorId.'/'.$urlKey.'-'.$category->getId().$suffix;
					if(!$baseUrlKey){
						$requestPath = $vendorId.'/'.$urlKey.'-'.$category->getId().$suffix;
					}
				}
				$urlRewrite = Mage::getModel('core/url_rewrite');
				$urlData = array(
					'is_system'		=> 1,
					'id_path'		=> $idPath,
					'request_path'	=> $requestPath,
					'target_path'	=> $targetPath,
					'is_vendors_url'=> 1,
					'store_id'		=> (int)$storeId,
				);
				$urlRewrite->setData($urlData);
				if($urlRewriteId) $urlRewrite->setId($urlRewriteId);
				$urlRewrite->save();
	    	}
    	}
    }

    /**
     * Move category to another parent node
     *
     * @param VES_VendorsCategory_Model_Category $category
     * @param VES_VendorsCategory_Model_Category $newParent
     * @param null|int $afterCategoryId
     * @return VES_VendorsCategory_Model_Mysql4_Category
     */
    public function changeParent(VES_VendorsCategory_Model_Category $category, VES_VendorsCategory_Model_Category $newParent,
                                 $afterCategoryId = null) {
        $table          = $this->getMainTable();
        $adapter        = $this->_getWriteAdapter();
        $levelFiled     = $adapter->quoteIdentifier('level');
        $pathField      = $adapter->quoteIdentifier('path');

        $position = $this->_processPositions($category, $newParent, $afterCategoryId);

        if($newParent->getPath())
            $newPath          = sprintf('%s/%s', $newParent->getPath(), $category->getId());
        else
            $newPath          = trim(sprintf('%s/%s', $newParent->getPath(), $category->getId()),'/');
        $newLevel         = $newParent->getLevel() + 1;
        $levelDisposition = $newLevel - $category->getLevel();

        /**
         * Update children nodes path
         */
        $adapter->update(
            $table,
            array(
                'path' => new Zend_Db_Expr('REPLACE(' . $pathField . ','.
                        $adapter->quote($category->getPath() . '/'). ', '.$adapter->quote($newPath . '/').')'
                    ),
                'level' => new Zend_Db_Expr( $levelFiled . ' + ' . $levelDisposition)
            ),
            array($pathField . ' LIKE ?' => $category->getPath() . '/%')
        );
        /**
         * Update moved category data
         */
        $data = array(
            'path'      => $newPath,
            'level'     => $newLevel,
            'sort_order'  =>$position,
            'parent_category_id' =>$newParent->getId()
        );
        $adapter->update($table, $data, array('category_id = ?' => $category->getId()));

        // Update category object to new data
        $category->addData($data);

        return $this;
    }

    /**
     * Process positions of old parent category children and new parent category children.
     * Get position for moved category
     *
     * @param VES_VendorsCategory_Model_Category $category
     * @param VES_VendorsCategory_Model_Category $newParent
     * @param null|int $afterCategoryId
     * @return int
     */
    protected function _processPositions($category, $newParent, $afterCategoryId) {
        $table          = $this->getMainTable();
        $adapter        = $this->_getWriteAdapter();
        $positionField  = $adapter->quoteIdentifier('sort_order');

        $bind = array(
            'sort_order' => new Zend_Db_Expr($positionField . ' - 1')
        );
        $where = array(
            'parent_category_id = ?'         => $category->getParentCategoryId(),
            $positionField . ' > ?' => $category->getSortOrder()
        );
        $adapter->update($table, $bind, $where);

        /**
         * Prepare position value
         */
        if ($afterCategoryId) {
            $select = $adapter->select()
                ->from($table,'sort_order')
                ->where('category_id = :category_id');
            $position = $adapter->fetchOne($select, array('category_id' => $afterCategoryId));

            $bind = array(
                'sort_order' => new Zend_Db_Expr($positionField . ' + 1')
            );
            $where = array(
                'parent_category_id = ?' => $newParent->getId(),
                $positionField . ' > ?' => $position
            );
            $adapter->update($table,$bind,$where);
        } elseif ($afterCategoryId !== null) {
            $position = 0;
            $bind = array(
                'sort_order' => new Zend_Db_Expr($positionField . ' + 1')
            );
            $where = array(
                'parent_category_id = ?' => $newParent->getId(),
                $positionField . ' > ?' => $position
            );
            $adapter->update($table,$bind,$where);
        } else {
            $select = $adapter->select()
                ->from($table,array('sort_order' => new Zend_Db_Expr('MIN(' . $positionField. ')')))
                ->where('parent_category_id = :parent_category_id');
            $position = $adapter->fetchOne($select, array('parent_category_id' => $newParent->getId()));
        }
        $position += 1;

        return $position;
    }
}