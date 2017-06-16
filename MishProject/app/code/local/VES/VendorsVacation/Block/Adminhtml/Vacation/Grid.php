<?php


class VES_VendorsVacation_Block_Adminhtml_Vacation_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('vacationGrid');
      $this->setDefaultSort('date_from');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
      $this->setUseAjax(true);
  }

	protected function _prepareCollection()
	{

		$vacations 		= Mage::helper('vendorsvacation')->getVacationData();
		$vendorIds 		= array();
		$newVacations 	= array();
		foreach($vacations as $vacation) {
			$newVacations[$vacation['vendor_id']] = $vacation;
			$vendorIds[] = $vacation['vendor_id'];
		}
	
		$collection = Mage::getModel('vendors/vendor')->getCollection()->addAttributeToFilter('entity_id',array('in'=>$vendorIds));
		foreach($collection as $vendor){
			$vacationData = $newVacations[$vendor->getId()];
			$vendor->setData('vacation_message',$vacationData['message']);
			$vendor->setData('vacation_date_from',$vacationData['date_from']);
			$vendor->setData('vacation_date_to',$vacationData['date_to']);
			$vendor->setData('vacation_product_status',$vacationData['product_status']);
		}
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('vendor_id', array(
			'header'    => Mage::helper('vendorsvacation')->__('Vendor ID'),
			'align'     =>'left',
			'index'     => 'vendor_id',
			'renderer'  =>  'VES_VendorsVacation_Block_Adminhtml_Widget_Grid_Column_Renderer_Vendor',
			'width'		=> '150px',
		));

		$this->addColumn('message', array(
			'header'    => Mage::helper('vendorsvacation')->__('Vacation Message'),
			'align'     =>'left',
			'index'     => 'vacation_message',
			'sortable'  => false,
			'filter'	=>false,
			'renderer'  =>  'VES_VendorsVacation_Block_Adminhtml_Widget_Grid_Column_Renderer_Message',
		));
		$dateFormatIso = Mage::app()->getLocale() ->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);


		$this->addColumn('date_from', array(
			'header'    => Mage::helper('vendorsvacation')->__('Date From'),
			'align'     =>'left',
			'index'     => 'vacation_date_from',
			'type'      => 'date',
			'default'   => '--',
			'format'    => $dateFormatIso,
			'sortable'      => false,
			'filter'	=>false,
		));

		$this->addColumn('date_to', array(
			'header'    => Mage::helper('vendorsvacation')->__('Date To'),
			'align'     =>'left',
			'index'     => 'vacation_date_to',
			'type'      => 'date',
			'default'   => '--',
			'format'    => $dateFormatIso,
			'sortable'      => false,
			'filter'	=>false,
		));

		/*$this->addColumn('vacation_status', array(
			'header'    => Mage::helper('vendorsvacation')->__('Vacation Status'),
			'align'     => 'left',
			'width'     => '80px',
			'index'     => 'vacation_status',
			'type'      => 'options',
			'options'   => array(
				VES_VendorsVacation_Model_Source_Vacation::VACATION_ON    => Mage::helper('vendorsvacation')->__('On'),
				VES_VendorsVacation_Model_Source_Vacation::VACATION_OFF   => Mage::helper('vendorsvacation')->__('Off'),
			),
		));*/

		$this->addColumn('product_status', array(
			'header'    => Mage::helper('vendorsvacation')->__('Disable All Products'),
			'align'     => 'left',
			'width'     => '80px',
			'index'     => 'vacation_product_status',
			'type'      => 'options',
			'options'   => array(
				VES_VendorsVacation_Model_Source_Product::PRODUCT_YES     => Mage::helper('vendorsvacation')->__('Yes'),
				VES_VendorsVacation_Model_Source_Product::PRODUCT_NO      => Mage::helper('vendorsvacation')->__('No'),
			),
			'sortable'      => false,
			'filter'	=>false,
		));

		return parent::_prepareColumns();
	}
	
	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

	public function getGridUrl(){
		return $this->getUrl('*/*/grid', array('_current'=>true));
	}
}