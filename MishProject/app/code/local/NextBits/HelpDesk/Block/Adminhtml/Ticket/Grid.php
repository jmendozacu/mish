<?php

class NextBits_HelpDesk_Block_Adminhtml_Ticket_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("ticketGrid");
				$this->setDefaultSort("ticket_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}
		 protected function _customerNameCondition($collection, $column) {
			
				if (!$value = trim($column->getFilter()->getValue())) {
					return;
				}

				if (is_numeric($value)) {   // Numeric value, this is likely to be a customer_id.
					$this->getCollection()->addCustomersToFilter($value);
				} else {    // Non numeric value.
					$inputKeywords = explode(' ', $value);

					$customerIds = array();
					$collection =Mage::getModel("helpdesk/ticket")->getCollection()->addCustomerNameToSelect();
					foreach ($collection as $key => $item) { 
						if (in_array($item->getCustomerId(), $customerIds)) { 
							continue;
						}

						$fullname = trim($item->getFullname());

						$match = false;
						if (count($inputKeywords) > 1) {    // Multiple name parts found in input.
							foreach ($inputKeywords as $keyword) {  // Input parts.
								if (strstr(strtolower($fullname), strtolower($keyword))) {  // Name part found in full name string.
									$match = true;
								}
							}
						} else {    // Single name part found in input.
							$firstname = trim($item->getFirstname());
							$lastname = trim($item->getLastname());

							if (strstr(strtolower($firstname), strtolower($value)) || strstr(strtolower($lastname), strtolower($value)) || strstr(strtolower($fullname), strtolower($value))) { // Name part found in one of the name variables.
								$match = true;
							}
						}

						if ($match) {   // Match found, add customer ID to the list to be filtered on.
							$customerIds[] = $item->getCustomerId();
						}
					}

					if (!empty($customerIds)) { // Customer IDs present, filter.
						$this->getCollection()->addCustomersToFilter($customerIds);
					}else
					{
						$this->getCollection()->addCustomersToFilter();
					}
				}
		}
		protected function _prepareCollection()
		{		
				$collection = Mage::getModel("helpdesk/ticket")->getCollection()->addCustomerNameToSelect();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("ticket_id", array(
				"header" => Mage::helper("helpdesk")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "ticket_id",
				));
                
				$this->addColumn("subject", array(
				"header" => Mage::helper("helpdesk")->__("Subject"),
				"index" => "subject",
				));
				$this->addColumn("message", array(
				"header" => Mage::helper("helpdesk")->__("Message"),
				"index" => "message",
				));
				$this->addColumn('status', array(
				'header' => Mage::helper('helpdesk')->__('Status'),
				'index' => 'status',
				'type' => 'options',
				'options'=>Mage::helper('helpdesk')->getAllStatus('grid'),				
				));
				$this->addColumn('priority', array(
				'header' => Mage::helper('helpdesk')->__('Priority'),
				'index' => 'priority',
				'type' => 'options',
				'options'=>Mage::helper('helpdesk')->getAllPriority('grid'),				
				));						
				$this->addColumn('created_date', array(
					'header'    => Mage::helper('helpdesk')->__('Created Date'),
					'index'     => 'created_date',
					'type'      => 'datetime',
				));
				$this->addColumn('updated_date', array(
					'header'    => Mage::helper('helpdesk')->__('Updated Date'),
					'index'     => 'updated_date',
					'type'      => 'datetime',
				));
				$this->addColumn('customer', array(
					'header'    => Mage::helper('helpdesk')->__('Customer'),
					'width'     => '150px',
					'index'     => 'customer_id',
					'format'    => '$fullname', // This makes the grid show the contents of each row's fullname variable, instead of the customer_id.
					'filter_condition_callback' => array($this, '_customerNameCondition')));
				
				$this->addColumn("order", array(
				"header" => Mage::helper("helpdesk")->__("Order"),
				"index" => "order",
				));
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}
		
		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('ticket_id');
			$this->getMassactionBlock()->setFormFieldName('ticket_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_ticket', array(
					 'label'=> Mage::helper('helpdesk')->__('Remove Ticket'),
					 'url'  => $this->getUrl('*/adminhtml_ticket/massRemove'),
					 'confirm' => Mage::helper('helpdesk')->__('Are you sure?')
				));
			return $this;
		}
}