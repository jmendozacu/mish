<?php
/**
 * Rule
 *
 * @category   VES
 * @package    VES_VBlock
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsCms_Model_Rule extends Mage_CatalogRule_Model_Rule
{
	/**
     * Getter for rule conditions collection
     *
     * @return Mage_CatalogRule_Model_Rule_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('vendorscms/rule_condition_combine');
    }
}