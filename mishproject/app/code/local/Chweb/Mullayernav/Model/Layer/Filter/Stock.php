<?php
class Chweb_Mullayernav_Model_Layer_Filter_Stock extends Mage_Catalog_Model_Layer_Filter_Abstract
{
    const DEFAULT_REQUEST_VAR   = 'in-stock';
    const REQUEST_VAR_PATH  = 'url_param';
    const STATE_LABEL       = 'label';
    /**
     * @var Easylife_StockFilter_Helper_Data
     */
    protected $_helper;
    /**
     * @var bool
     */
    protected $_activeFilter = false;
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_helper = Mage::helper('mullayernav');
        $requestVar = trim($this->_helper->getConfigValue(self::REQUEST_VAR_PATH));
        if (!$requestVar) {
            $requestVar = self::DEFAULT_REQUEST_VAR;
        }
         $this->_requestVar = 'stock';
    }
     protected function _initItems() {
        $data = $this->_getItemsData();
		
		//echo '<pre>'; print_r($data);die;

        $items = array();
        foreach ($data as $itemData) {
            $obj = new Varien_Object();
            $obj->setData($itemData);
            $obj->setUrl($itemData['value']);

            $items[] = $obj;
        }
        $this->_items = $items;
        return $this;
    }
    /**
     * Apply stock  filter to layer
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Mage_Core_Block_Abstract $filterBlock
     * @return  $this
     */
	  public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
		
        $filter = Mage::helper('mullayernav')->getParam($this->_requestVar);
        $filter = explode('-', $filter);
        //echo '<pre>';print_r($filter);die;
	    if($filter[0]=='')
		{
		return $this;	
			
		}
        $ids = array();    
        foreach ($filter as $id){
            $id = intVal($id);
			//echo 'kk='.$id; die;
            if ($id)
			$ids[] = $id;    
        } 
        if (count($ids)>0){
            $this->applyMultipleValuesFilter($ids);     
        }
        
        $this->setActiveState($ids);
        return $this;
    }

    protected function applyMultipleValuesFilter($ids)
    {
        $collection = $this->getLayer()->getProductCollection();
      //  $attribute  = $this->getAttributeModel();
        $table = Mage::getSingleton('core/resource')->getTableName('cataloginventory/stock_status');
		        $stockid = array();    
        foreach ($ids as $id){
           // $id = intVal($id);
			//echo 'kk='.$id; die;
            if ($id==2)$id=0;
			$stockid[] = $id;    
        } 
		
        //echo  print_r($ids); die;
        $alias = 'stock_statusnew';
        $collection->getSelect()->join(
            array($alias => $table),
            $alias.'.product_id=e.entity_id',
            array()
        )
        ->where($alias.'.stock_status IN (?)', $stockid);
		//echo count($ids);die;
        if (count($ids)>0){
            $collection->getSelect()->distinct(true); 
        }
       //echo $collection->getSelectSql(true);die;
        return $this;
    }   
    
/*	 public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
  //  public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = $request->getParam($this->getRequestVar(), null);
		$filter = Mage::helper('mullayernav')->getParam($this->_requestVar);
		    echo    $table = Mage::getSingleton('core/resource')->getTableName('cataloginventory/stock_status');die;

		//echo 'dddd='.$filter;die;
        if (is_null($filter)) {
            return $this;
        }
		
        $this->_activeFilter = true;
        $filter = (int)(bool)$filter;
        $collection = $this->getLayer()->getProductCollection();
		$collection->getSelect()->join(array('stock_status'=>'cataloginventory_stock_status'), ' e.entity_id = stock_status.product_id');

        $collection->getSelect()->where('stock_status.stock_status = ?', $filter);
		//echo $collection->__toString();die;
		//echo $collection->getSelectSql(true);die;
        $this->getLayer()->getState()->addFilter(
            $this->_createItem($this->getLabel($filter), $filter)
        );
        return $this;
    }*/

    /**
     * Get filter name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_helper->getConfigValue(self::STATE_LABEL);
    }

    /**
     * Get data array for building status filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        
        $key = $this->getLayer()->getStateKey();
        $key .= Mage::helper('mullayernav')->getCacheKey($this->_requestVar);

        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {
            $data = array();
			  $n=1;
            foreach ($this->getStatuses() as $status) {
				if($this->getProductsCount($status)>0){
				    $data[] = array(
                    'label' => $this->getLabel($status),
                    'value' => $status,
					'stocks' => $n,
                    'count' => $this->getProductsCount($status)
                );
				}
				$n++;
            }

            $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }

    /**
     * get available statuses
     * @return array
     */
    public function getStatuses() {
        return array(
            Mage_CatalogInventory_Model_Stock::STOCK_IN_STOCK,
            Mage_CatalogInventory_Model_Stock::STOCK_OUT_OF_STOCK
        );
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        return array(
            Mage_CatalogInventory_Model_Stock::STOCK_IN_STOCK => Mage::helper('mullayernav')->__('In Stock'),
            Mage_CatalogInventory_Model_Stock::STOCK_OUT_OF_STOCK => Mage::helper('mullayernav')->__('Out of stock'),
        );
    }

    /**
     * @param $value
     * @return string
     */
    public function getLabel($value)
    {
        $labels = $this->getLabels();
        if (isset($labels[$value])) {
            return $labels[$value];
        }
        return '';
    }
    public function getProductsCount($value)
    {
        $collection = $this->getLayer()->getProductCollection();
        $select = clone $collection->getSelect();
		 $table = Mage::getSingleton('core/resource')->getTableName('cataloginventory/stock_status');
$select->reset(Zend_Db_Select::GROUP);
        // reset columns, order and limitation conditions
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
		$select->join(array('stock_status'=>$table), ' e.entity_id = stock_status.product_id');
         $select->reset(Zend_Db_Select::COLUMNS);
        $select->where('stock_status.stock_status = ?', $value);
        $select->columns(array('count' => new Zend_Db_Expr("COUNT(e.entity_id)")));
		//echo $select->__toString();die;
		
        return $collection->getConnection()->fetchOne($select);
    }
}
