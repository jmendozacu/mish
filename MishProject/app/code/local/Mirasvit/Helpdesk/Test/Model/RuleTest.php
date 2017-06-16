<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Help Desk MX
 * @version   1.1.0
 * @build     1285
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Helpdesk_Model_RuleTest extends EcomDev_PHPUnit_Test_Case
{
    protected function setUp()
    {
        parent::setUp();
    }

    protected function getConditions($field, $operator, $value)
    {
        $condition = array(
            'type' => 'helpdesk/rule_condition_combine',
            'attribute' => '',
            'operator' => '',
            'value' => '1',
            'is_value_processed' => '',
            'aggregator' => 'all',
            'conditions' => array(
                    '0' => array(
                            'type' => 'helpdesk/rule_condition_ticket',
                            'attribute' => $field,
                            'operator' => $operator,
                            'value' => $value,
                            'is_value_processed' => '',
                        )
                )
        );
        return serialize($condition);
    }
    /**
     * @test
     * @loadFixture data
     *
     * @doNotIndex catalog_product_price
     */
    public function last_reply_byTest() {
        $ticket = Mage::getModel('helpdesk/ticket')->load(2);
        $rule = Mage::getModel('helpdesk/rule')->load(2);
        $rule->setConditionsSerialized($this->getConditions('last_reply_by', '==', 'customer'));
        $rule->afterLoad();

        $this->assertFalse($rule->validate($ticket));

        $customer = new Varien_Object();
        $customer->setName('John Doe');
        $customer->setEmail('john@example.com');
        $ticket->addMessage('message 1', $customer, false, true);

        $this->assertTrue($rule->validate($ticket));
    }

    /**
     * @test
     * @loadFixture data
     *
     * @doNotIndex catalog_product_price
     */
    public function hours_sinceTest() {
        $ticket = Mage::getModel('helpdesk/ticket')->load(2);
        $rule = Mage::getModel('helpdesk/rule')->load(2);
        $rule->setConditionsSerialized($this->getConditions('hours_since_created_at', '>=', 1));
        $rule->afterLoad();

        $this->assertTrue($rule->validate($ticket));

        $rule = Mage::getModel('helpdesk/rule')->load(2);
        $rule->setConditionsSerialized($this->getConditions('hours_since_updated_at', '<=', 1));
        $rule->afterLoad();

        $this->assertFalse($rule->validate($ticket));
        $ticket->setName('aaa')->save();

        $this->assertTrue($rule->validate($ticket));
    }

    /**
     * @test
     * @loadFixture data
     *
     * @doNotIndex catalog_product_price
     */
    public function tagsTest() {
        $ticket = Mage::getModel('helpdesk/ticket')->load(2);
        $rule = Mage::getModel('helpdesk/rule')->load(2);
        $rule->setConditionsSerialized($this->getConditions('tags', '!{}', 'aaa'));
        $rule->afterLoad();
        $this->assertTrue($rule->validate($ticket));

        Mage::helper('helpdesk/tag')->addTags($ticket, 'aaa, bbb');

        $this->assertFalse($rule->validate($ticket));

    }

    /**
     * @test
     * @loadFixture data
     *
     * @doNotIndex catalog_product_price
     */
    public function updatedAtTest() {

        $ticket = Mage::getModel('helpdesk/ticket')->load(2);
        //у мадженто есть бага в функции gmtTimestamp в одном случае дата в таймзоне сервера, в другом в таймзоне магазина
        $ticket->setUpdatedAt(gmdate('Y-m-d H:i:s', time() - 60 * 60 * 119));

        $rule = Mage::getModel('helpdesk/rule')->load(2);
        $rule->setConditionsSerialized($this->getConditions('hours_since_updated_at', '>=', '120'));
        $rule->afterLoad();
        $this->assertFalse($rule->validate($ticket));

        $ticket->setUpdatedAt(gmdate('Y-m-d H:i:s', time() - 60 * 60 * 121));
        $this->assertTrue($rule->validate($ticket));

    }
}