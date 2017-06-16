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


class Mirasvit_Helpdesk_Block_Adminhtml_Report_Ticket_Chart extends Mage_Core_Block_Template
{
    public function getCollection()
    {
        return $this->getGrid()->getCollection();
    }

    public function isShowChart()
    {
        $collection = $this->getCollection();

        if ($this->getCollection()->count() > 1 && $this->getFilterData()->getReportType() == 'all') {
            return true;
        }

        return false;
    }
}