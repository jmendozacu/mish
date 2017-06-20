<?php

/**
 * Open Biz Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file OPEN-BIZ-LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://mageconsult.net/terms-and-conditions
 *
 * @category   Magecon
 * @package    Magecon_AdvancedStockBar
 * @version    1.0.0
 * @copyright  Copyright (c) 2012 Open Biz Ltd (http://www.mageconsult.net)
 * @license    http://mageconsult.net/terms-and-conditions
 */
class Magecon_AdvancedStockBar_Block_AdvancedStockBarGrouped extends Mage_Catalog_Block_Product_View_Type_Grouped {

	protected function _toHtml() {
        $html = $this->getChildHtml('advancedstockbar');
        $html .= parent::_toHtml();
        return $html;
    }
	
    public function parseComment($comment, $productId) {
        $product = Mage::getModel('catalog/product')->load($productId);
		$comment = preg_replace("/\[qty\]/", (int) $this->getProductQtyMin(), $comment);
        $comment = preg_replace("/\[product_name\]/", $product->getName(), $comment);
        $comment = preg_replace("/\[sku\]/", Mage::getModel('catalog/product')->load($productId)->getSku(), $comment);
        return $comment;
    }

    public function sortThresholds($array, $index, $order = 'asc', $natsort = FALSE, $case_sensitive = FALSE) {
        if (is_array($array) && count($array) > 0) {
            foreach (array_keys($array) as $key)
                $temp[$key] = $array[$key][$index];
            if (!$natsort)
                ($order == 'asc') ? asort($temp) : arsort($temp);
            else {
                ($case_sensitive) ? natsort($temp) : natcasesort($temp);
                if ($order != 'asc')
                    $temp = array_reverse($temp, TRUE);
            }
            foreach (array_keys($temp) as $key)
                (is_numeric($key)) ? $sorted[] = $array[$key] : $sorted[$key] = $array[$key];
            return $sorted;
        }
        return $array;
    }

    public function getAttributes() {
        $product = $this->getProduct();
        $attributes = $product->getTypeInstance()->getConfigurableAttributes();
        $attributeIds = array();
        foreach ($attributes as $attr) {
            $attributeIds[] = $attr->getAttributeId();
        }
        return $attributeIds;
    }

    public function getAssocProducts() {
        $product = $this->getProduct();
        $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $product);
        return $childProducts;
    }

    public function getConfig($field) {
        return Mage::getStoreConfig('advancedstockbar/advancedstockbar_settings/' . $field);
    }

    public function getStockBarInfo($product) {
        $threshold = array();
        $unserializedThreshold = unserialize($this->getConfig('threshold'));
        foreach ($unserializedThreshold as &$_t) {
            array_push($threshold, $_t);
        }
        $threshold = $this->sortThresholds($threshold, 'threshold_value');

		$_invAmt = (int) $this->getProductQtyMin();
        $_barValue = (100 * $_invAmt) / $threshold[count($threshold) - 1]['threshold_value'];

        if (count($threshold) >= 1) {
            if ($product->isAvailable() && $product->isInStock()) {
                for ($i = 0; $i < count($threshold) - 1; $i++) {
                    if ($_invAmt <= (int) $threshold[$i]['threshold_value']) {
                        $_stockComment = $this->parseComment($threshold[$i]['status'], $product->getId());
                        $_barColor = $threshold[$i]['color'];
                        break;
                    } else if ($_invAmt > (int) $threshold[$i]['threshold_value'] && $_invAmt <= (int) $threshold[$i + 1]['threshold_value']) {
                        $_stockComment = $this->parseComment($threshold[$i + 1]['status'], $product->getId());
                        $_barColor = $threshold[$i + 1]['color'];
                        break;
                    }
                }

                if ($_invAmt > (int) $threshold[count($threshold) - 1]['threshold_value']) {
                    $_barValue = 100;
                    $_stockComment = $this->parseComment($this->getConfig('fullstock_status'), $product->getId());
                    $_barColor = $threshold[count($threshold) - 1]['color'];
                }
            } else {
                $_barValue = 0;
                $_stockComment = $this->parseComment($this->getConfig('nostock_status'), $product->getId());
                $_barColor = $threshold[0]['color'];
            }
        }
        $stockBarInfo = array(
            "bar_value" => $_barValue,
            "bar_height" => $this->getConfig('height'),
            "bar_color" => $_barColor,
            "stock_comment" => $_stockComment
        );

        return $stockBarInfo;
    }
	
	public function getProductQtyMin() {
		$_associatedProducts = $this->getAssociatedProducts();
		$_hasAssociatedProducts = count($_associatedProducts) > 0;
		
		if ($_hasAssociatedProducts) {
			$minQty = 1000000;
			foreach ($_associatedProducts as $_product) {
				$qty = $this->getProductQty($_product);
				
				if ($qty < $minQty) {
					$minQty = $qty;
				}
			}
		}
		
		return $minQty;
	}
	
	public function getProductQty($product) {
		return Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
	}
}