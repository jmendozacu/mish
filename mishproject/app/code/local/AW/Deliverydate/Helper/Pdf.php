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
 * @package    AW_Deliverydate
 * @version    1.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_Deliverydate_Helper_Pdf {
    
    const WORDS_WRAP = 70;

    public function insertDeliveryDate(&$page, $order, &$yShipments) {

        $date = Mage::helper('core')->formatDate($order->getAwDeliverydateDate(), Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, null);
        $page->drawText(Mage::helper('deliverydate')->__('Delivery Date:'), 285, $yShipments - 7, 'UTF-8');
        $yShipments -=7;
        $page->drawText($date, 285, $yShipments - 7, 'UTF-8');
        $yShipments -=10;
        $page->drawText(Mage::helper('deliverydate')->__('Delivery Notice:'), 285, $yShipments - 7, 'UTF-8');
        $yShipments -=7;
        $description = $order->getAwDeliverydateNotice();

        $newLine = null;
        $description = $this->preProcess($description);
        $i = 0;
        foreach ($description as $data) {
            if ($i) {
                $newLine .= "@newline@";
            }
            $newLine .= $this->iconv_wordwrap($data, self::WORDS_WRAP, "@newline@");
            $i++;
        }

        $description = explode("@newline@", $newLine);
        foreach ($description as $data) {
            $page->drawText($data, 285, $yShipments - 7, 'UTF-8');
            $yShipments -=7;
        }
    }

    public function preProcess($string) {

        $string = strip_tags(htmlspecialchars_decode($string));
        $string = preg_replace("#\n|\r\n#isu", "@newline@", $string);

        return explode("@newline@", $string);
    }

    public function iconv_wordwrap($string, $width = 75, $break = "\n", $cut = false, $charset = 'utf-8') {


        /* If mbstring lib is not available return not truncated string */
        if (!in_array('mbstring', get_loaded_extensions())) {
            return $string;
        }

        /* Get endoding fallback to utf-8 */
        if (!$encoding = mb_detect_encoding($string)) {
            $charset = 'UTF-8';
        }

        $stringWidth = mb_strlen($string, $charset);
        $breakWidth = mb_strlen($break, $charset);

        if (strlen($string) === 0) {
            return '';
        } elseif ($breakWidth === null) {
            throw new Zend_Text_Exception('Break string cannot be empty');
        } elseif ($width === 0 && $cut) {
            throw new Zend_Text_Exception('Can\'t force cut when width is zero');
        }

        $result = '';
        $lastStart = $lastSpace = 0;

        for ($current = 0; $current < $stringWidth; $current++) {
            $char = mb_substr($string, $current, 1, $charset);

            if ($breakWidth === 1) {
                $possibleBreak = $char;
            } else {
                $possibleBreak = mb_substr($string, $current, $breakWidth, $charset);
            }

            if ($possibleBreak === $break) {
                $result .= mb_substr($string, $lastStart, $current - $lastStart + $breakWidth, $charset);
                $current += $breakWidth - 1;
                $lastStart = $lastSpace = $current + 1;
            } elseif ($char === ' ') {
                if ($current - $lastStart >= $width) {
                    $result .= mb_substr($string, $lastStart, $current - $lastStart, $charset) . $break;
                    $lastStart = $current + 1;
                }

                $lastSpace = $current;
            } elseif ($current - $lastStart >= $width && $cut && $lastStart >= $lastSpace) {
                $result .= mb_substr($string, $lastStart, $current - $lastStart, $charset) . $break;
                $lastStart = $lastSpace = $current;
            } elseif ($current - $lastStart >= $width && $lastStart < $lastSpace) {
                $result .= mb_substr($string, $lastStart, $lastSpace - $lastStart, $charset) . $break;
                $lastStart = $lastSpace = $lastSpace + 1;
            }
        }

        if ($lastStart !== $current) {
            $result .= mb_substr($string, $lastStart, $current - $lastStart, $charset);
        }

        return $result;
    }

}