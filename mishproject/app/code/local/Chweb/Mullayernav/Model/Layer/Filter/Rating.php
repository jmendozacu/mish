<?php
/**
* Chweb Multi Layered Navigation 
* 
* @category     Chweb
* @package      Chweb_Mullayernav 
* @copyright    Copyright (c) 2014-2015 Chweb (http://www.chaudharyweb.com/)
* @author       Chweb (Rajesh chaudhary)  
* @version      Release: 1.0.0
* @Class        Chweb_Mullayernav_Model_Layer_Filter_Rating   
*/
class Chweb_Mullayernav_Model_Layer_Filter_Rating  extends Mage_Catalog_Model_Layer_Filter_Abstract
{

	const FILTER_ON_SALE = 1;
	const FILTER_NOT_ON_SALE = 2;	
	const RATING_FILTER_ON_RATED 	= 6;
	const RATING_FILTER_RATED_1 	= 1;
	const RATING_FILTER_RATED_2 	= 2;
	const RATING_FILTER_RATED_3 	= 3;
	const RATING_FILTER_RATED_4 	= 4;
	const RATING_FILTER_RATED_5 	= 5;
	

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_requestVar = 'rating';
	}
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
		$select = $this->getLayer()->getProductCollection()->getSelect();
		/* @var $select Zend_Db_Select */

	    
       $select->joinLeft(array('rs'=>'review_entity_summary'), ' e.entity_id = rs.entity_pk_value and cat_index.store_id = rs.store_id ');
        $select->columns(array('avg_Summary' => new Zend_Db_Expr('avg(rating_summary)')));
        $select->group("e.entity_id");
			//$rating=array();
		//echo '<pre>';print_r($ids);
		$rating='';
		foreach($ids  as  $filter)
		{
			
		if ($filter== self::RATING_FILTER_ON_RATED) {
			
			//$select->where('(rs.rating_summary = 0 or rs.rating_summary is NULL )');
			if($rating=='')
			{
			$rating.='(rs.rating_summary = 0 or rs.rating_summary is NULL )';	
			}else{
			$rating.=' OR (rs.rating_summary = 0 or rs.rating_summary is NULL )';				
			}
			
		} else if ($filter == self::RATING_FILTER_RATED_1) {
			if($rating=='')
			{
			$rating.='(rs.rating_summary > 0 and rs.rating_summary <= 20)';	
			}else{
			$rating.='OR (rs.rating_summary > 0 and rs.rating_summary <= 20)';					
			}
		
		}else if ($filter == self::RATING_FILTER_RATED_2) {
			if($rating=='')
			{
			$rating.='(rs.rating_summary > 20 and rs.rating_summary <= 40)';	
			}else{
			$rating.=' OR (rs.rating_summary > 20 and rs.rating_summary <= 40)';					
			}
				
		}else if ($filter == self::RATING_FILTER_RATED_3) {
			if($rating=='')
			{
			$rating.='(rs.rating_summary > 40 and rs.rating_summary <= 60)';	
			}else{
			$rating.=' OR (rs.rating_summary > 40 and rs.rating_summary <= 60)';					
			}
			
		}else if ($filter == self::RATING_FILTER_RATED_4) {
  		   if($rating=='')
			{
			$rating.='(rs.rating_summary > 60 and rs.rating_summary <= 80)';	
			}else{
			$rating.=' OR (rs.rating_summary > 60 and rs.rating_summary <= 80)';					
			}
			
		}else if ($filter == self::RATING_FILTER_RATED_5) {
		 if($rating=='')
			{
			$rating.='(rs.rating_summary > 80 and rs.rating_summary <= 100)';	
			}else{
				//echo 'kkk='.$rating;
			$rating.=' OR (rs.rating_summary > 80 and rs.rating_summary <= 100)';					
			}
			
		}
			
		}
		//echo $rating;  
		$select->where($rating);
		//echo $select->__toString();
		//die;
		
      //  $attribute  = $this->getAttributeModel();_
       // $table = Mage::getSingleton('core/resource')->getTableName('review/entity_summary');
		      
       
            $select->distinct(true); 
       // echo $select->__toString();
		//die;
       // echo $collection->getSelectSql(true);die;
	   Mage::register('chweb_rating_filter', true);
        return $this;
    }   
    
	/**
	 * Get filter name
	 *
	 * @return string
	 */
	public function getName()
	{
		return Mage::helper('mullayernav')->__('Rating');
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
	 * Get data array for building sale filter items
	 *
	 * @return array
	 */
	protected function _getItemsData()
	{
		
		//echo 'ddddddddddd';die;
		$data = array();
		$status = $this->_getCount();
		
	     $key = $this->getLayer()->getStateKey();
        $key .= Mage::helper('mullayernav')->getCacheKey($this->_requestVar);

        $data = $this->getLayer()->getAggregator()->getCacheData($key);
		
		if (isset($status[self::RATING_FILTER_ON_RATED]) && $status[self::RATING_FILTER_ON_RATED]) 
		$data[] = array(
				'label' => Mage::helper('mullayernav')->__(Mage::getStoreConfig("mullayernav/option/nonrated")),
				'value' => self::RATING_FILTER_ON_RATED,
				'count' => $status[self::RATING_FILTER_ON_RATED],
		);

		if (isset($status[self::RATING_FILTER_RATED_1]) && $status[self::RATING_FILTER_RATED_1])
		$data[] = array(
				'label' => Mage::helper('mullayernav')->__(Mage::getStoreConfig("mullayernav/option/onestar")),
				'value' => self::RATING_FILTER_RATED_1,
				'count' => $status[self::RATING_FILTER_RATED_1],
		);
		
		
		if (isset($status[self::RATING_FILTER_RATED_2]) && $status[self::RATING_FILTER_RATED_2])
			$data[] = array(
					'label' => Mage::helper('mullayernav')->__(Mage::getStoreConfig("mullayernav/option/twostar")),
					'value' => self::RATING_FILTER_RATED_2,
					'count' => $status[self::RATING_FILTER_RATED_2],
			);
		
		
		if (isset($status[self::RATING_FILTER_RATED_3]) && $status[self::RATING_FILTER_RATED_3])
			$data[] = array(
					'label' => Mage::helper('mullayernav')->__(Mage::getStoreConfig("mullayernav/option/threestar")),
					'value' => self::RATING_FILTER_RATED_3,
					'count' => $status[self::RATING_FILTER_RATED_3],
			);
		
		
		
		if (isset($status[self::RATING_FILTER_RATED_4]) && $status[self::RATING_FILTER_RATED_4])
			$data[] = array(
					'label' => Mage::helper('mullayernav')->__(Mage::getStoreConfig("mullayernav/option/fourstar")),
					'value' => self::RATING_FILTER_RATED_4,
					'count' => $status[self::RATING_FILTER_RATED_4],
			);
		
		if (isset($status[self::RATING_FILTER_RATED_5]) && $status[self::RATING_FILTER_RATED_5])
			$data[] = array(
					'label' => Mage::helper('mullayernav')->__(Mage::getStoreConfig("mullayernav/option/fivestar")),
					'value' => self::RATING_FILTER_RATED_5,
					'count' => $status[self::RATING_FILTER_RATED_5],
			);
		
		  $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
		return $data;
	}

	protected function _getCount()
	{
		// Clone the select
		$select = clone $this->getLayer()->getProductCollection()->getSelect();
		/* @var $select Zend_Db_Select */
		
			
		$select->reset(Zend_Db_Select::ORDER);
		$select->reset(Zend_Db_Select::LIMIT_COUNT);
		$select->reset(Zend_Db_Select::LIMIT_OFFSET);
		//$select->reset(Zend_Db_Select::WHERE);
		 $oldWhere = $select->getPart(Varien_Db_Select::WHERE);
        $newWhere = array();
              
        foreach ($oldWhere as $cond){
           if (!strpos($cond, 'rating_summary'))
               $newWhere[] = $cond;
        }
  
        if ($newWhere && substr($newWhere[0], 0, 3) == 'AND')
           $newWhere[0] = substr($newWhere[0],3);        
        
        $select->setPart(Varien_Db_Select::WHERE, $newWhere);
  //echo '<pre>'; print_r($newWhere);die;
		if (!Mage::registry('chweb_rating_filter')) {
		$select->joinLeft(array('rs'=>'review_entity_summary'), ' e.entity_id = rs.entity_pk_value and cat_index.store_id = rs.store_id ');
		$select->columns(array('avg_Summary' => new Zend_Db_Expr('avg(rating_summary)')));
		$select->group("e.entity_id");
		}
    
	//echo $select->__toString();die;
$sql=str_replace('`price_index`.`price`,','',$select->__toString());
	$sql=str_replace('`price_index`.`tax_class_id`,','',$sql);
		// Count the on sale and not on sale
// 		$sql = 'SELECT IF(avg_Summary > 0 and avg_Summary < 21, "1", "Null") as on_sale, COUNT(*) as count from ('
// 				.$select->__toString().') AS q GROUP BY on_sale';

		$sql = 'SELECT CASE 
					when avg_Summary > 0 and avg_Summary <= 20 	then 1
					when avg_Summary > 20 and avg_Summary <= 40 then 2			
					when avg_Summary > 40 and avg_Summary <= 60 then 3
					when avg_Summary > 60 and avg_Summary <= 80 then 4
					when avg_Summary > 80 and avg_Summary <= 100 then 5
					ELSE
					6
				END
				as on_sale, COUNT(*) as count from ('
				.$sql.') AS q GROUP BY on_sale';
		
		

		$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		/* @var $connection Zend_Db_Adapter_Abstract */

		return $connection->fetchPairs($sql);
	}

}