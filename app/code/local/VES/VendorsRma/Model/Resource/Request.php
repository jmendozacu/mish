<?php

class VES_VendorsRma_Model_Resource_Request extends Mage_Eav_Model_Entity_Abstract
{
    public function _construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType(VES_VendorsRma_Model_Request::ENTITY)
            ->setConnection('vendorsrma_write', 'vendorsrma_write');
    }

    /**
     * Retrieve customer entity default attributes
     *
     * @return array
     */
    protected function _getDefaultAttributes()
    {
        return array(
            'entity_type_id',
            'attribute_set_id',
            'created_at',
            'updated_at',
            'increment_id',
            'store_id',
            'website_id'
        );
    }

    /**
     * Check customer scope, email and confirmation key before saving
     *
     * @param Mage_Customer_Model_Customer $customer
     * @throws Mage_Customer_Exception
     * @return Mage_Customer_Model_Resource_Customer
     */
    protected function _beforeSave(Varien_Object $request)
    {
        parent::_beforeSave($request);
        $this->setNewIncrementId($request);
    }
}