<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Randomprice
 * @version    1.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_Randomprice_Model_Source_Running extends AW_Randomprice_Model_Source_Abstract {
    const PENDING_LABEL = 'Pending';
    const STARTED_LABEL = 'Started';
    const ENDED_LABEL = 'Ended';

    public function toOptionArray() {
        $_helper = $this->_getHelper();
        return array(
            AW_Randomprice_Model_Randomprice::STATUS_PENDING => $_helper->__(self::PENDING_LABEL),
            AW_Randomprice_Model_Randomprice::STATUS_STARTED => $_helper->__(self::STARTED_LABEL),
            AW_Randomprice_Model_Randomprice::STATUS_ENDED => $_helper->__(self::ENDED_LABEL)
        );
    }

}
