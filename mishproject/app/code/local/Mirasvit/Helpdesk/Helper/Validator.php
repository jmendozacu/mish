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


class Mirasvit_Helpdesk_Helper_Validator extends Mirasvit_MstCore_Helper_Validator_Abstract
{
    public function testMagentoCrc()
    {
        $filter = array(
            'app/code/core/Mage/Core',
            'js'
        );
        return Mage::helper('mstcore/validator_crc')->testMagentoCrc($filter);
    }

    public function testMirasvitCrc()
    {
        $modules = array('Helpdesk');
        return Mage::helper('mstcore/validator_crc')->testMirasvitCrc($modules);
    }

    public function testISpeedCache()
    {
        $result = self::SUCCESS;
        $title = 'My_Ispeed';
        $description = array();
        if (Mage::helper('mstcore')->isModuleInstalled('My_Ispeed')) {
            $result = self::INFO;
            $description[] = 'Extension My_Ispeed is installed. Please, go to the Configuration > Settings > I-Speed > General Configuration and add \'helpdesk\' to the list of Ignored URLs. Then clear ALL cache.';
        }

        return array($result, $title, $description);
    }

    public function testMgtVarnishCache()
    {
        $result = self::SUCCESS;
        $title = 'Mgt_Varnish';
        $description = array();
        if (Mage::helper('mstcore')->isModuleInstalled('Mgt_Varnish')) {
            $result = self::INFO;
            $description[] = 'Extension Mgt_Varnish is installed. Please, go to the Configuration > Settings > MGT-COMMERCE.COM > Varnish and add \'helpdesk\' to the list of Excluded Routes. Then clear ALL cache.';
        }

        return array($result, $title, $description);
    }

    public function testTables()
    {
        $tables = array(
            'admin/user',
            'helpdesk/attachment',
            'helpdesk/department',
            'helpdesk/department_user',
            'helpdesk/email',
            'helpdesk/field',
            'helpdesk/field_store',
            'helpdesk/gateway',
            'helpdesk/history',
            'helpdesk/message',
            'helpdesk/pattern',
            'helpdesk/permission',
            'helpdesk/permission_department',
            'helpdesk/priority',
            'helpdesk/rule',
            'helpdesk/satisfaction',
            'helpdesk/status',
            'helpdesk/tag',
            'helpdesk/template',
            'helpdesk/template_store',
            'helpdesk/ticket',
            'helpdesk/ticket_aggregated',
            'helpdesk/ticket_tag',
        );
        return $this->dbCheckTables($tables);
    }
}