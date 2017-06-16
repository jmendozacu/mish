<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Asynchronous Cache
 * @version   1.0.0
 * @revision  125
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_AsyncCache_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_variablePrefix = 'asynccache_';

    public function setVariableValue($code, $value)
    {
        $code     = $this->_variablePrefix.$code;
        $variable = Mage::getModel('core/variable');
        $variable = $variable->loadByCode($code);

        if (!$variable->getId()) {
            $variable->setCode($code)
                ->setName($code);
        }

        $variable->setPlainValue($value)
            ->setHtmlValue($value)
            ->save();

    }

    public function getVariableValue($code)
    {
        $code     = $this->_variablePrefix.$code;

        $variable = Mage::getModel('core/variable');
        $variable = $variable->loadByCode($code);

        return $variable->getPlainValue();
    }

    public function formatElapsedTime($elapsed)
    {
        $tokens = array (
            31536000 => 'year',
            2592000  => 'month',
            604800   => 'week',
            86400    => 'day',
            3600     => 'hour',
            60       => 'minute',
            1        => 'second'
        );

        foreach ($tokens as $unit => $text) {
            if ($elapsed < $unit && $unit != 1) {
                continue;
            }
            $numberOfUnits = floor($elapsed / $unit);
            $elapsed -= $numberOfUnits * $unit;
            $result[] = $numberOfUnits.' '.$text.($numberOfUnits > 1 ? 's' : '');
        }

        return implode(' ', $result);
    }
}
