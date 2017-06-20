<?php
    class NextBits_HelpDesk_Model_Mysql4_Ticket_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
    {

		public function _construct(){
			$this->_init("helpdesk/ticket");
		}
		public function addCustomerNameToSelect() {
				$firstnameAttr = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'firstname');
				$lastnameAttr = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'lastname');
				$customer_entity_varchar = Mage::getSingleton('core/resource')->getTableName('customer_entity_varchar');
				$this->getSelect()
				->join(array('ce1' => $customer_entity_varchar), 'ce1.entity_id=main_table.customer_id', array('firstname' => 'value'))
				->where('ce1.attribute_id='.$firstnameAttr->getAttributeId()) // Attribute code for firstname.
				->join(array('ce2' => $customer_entity_varchar), 'ce2.entity_id=main_table.customer_id', array('lastname' => 'value'))
				->where('ce2.attribute_id='.$lastnameAttr->getAttributeId()) // Attribute code for lastname.
				->columns(new Zend_Db_Expr("CONCAT(`ce1`.`value`, ' ',`ce2`.`value`) AS fullname"));
				return $this;
		}
		  public function addCustomersToFilter($customerIds) {
				if (!is_array($customerIds)) {
					$customerIds = array($customerIds);
				}

				foreach ($customerIds as $key => $customer) {
					if ($customer instanceof Mage_Customer_Model_Customer) {
						$customerIds[$key] = $customer->getId();
					}
				}

				$this->addFieldToFilter('customer_id', array('in' => $customerIds));

				return $this;
		}
    }
	 