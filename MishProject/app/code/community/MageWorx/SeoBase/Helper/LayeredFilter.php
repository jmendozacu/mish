<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Helper_LayeredFilter extends Mage_Core_Helper_Abstract
{

    /**
     * Admin config setting
     */
    const CATEGORY_LN_CANONICAL_OFF          = 0;
    const CATEGORY_LN_CANONICAL_USE_FILTERS  = 1;
    const CATEGORY_LN_CANONICAL_CATEGORY_URL = 2;

    /**
     * Attribut individual setting
     */
    const ATTRIBUTE_LN_CANONICAL_BY_CONFIG    = 0;
    const ATTRIBUTE_LN_CANONICAL_USE_FILTERS  = 1;
    const ATTRIBUTE_LN_CANONICAL_CATEGORY_URL = 2;

    public function isLNFriendlyUrlsEnabled()
    {
        return false;
    }

    /**
     * Determines by global value from a config and to value (based on attributes setting and position)
     * existence of filters in canonical url.
     *
     * @return boolean
     */
    public function isIncludeLNFiltersToCanonicalUrl()
    {
        $enableByConfig  = Mage::helper('mageworx_seobase')->isIncludeLNFiltersToCanonicalUrlByConfig();
        $answerByFilters = $this->isIncludeLNFiltersToCanonicalUrlByFilters();

        if ($enableByConfig == self::CATEGORY_LN_CANONICAL_USE_FILTERS && $answerByFilters == self::ATTRIBUTE_LN_CANONICAL_CATEGORY_URL) {
            return false;
        }

        if ($enableByConfig == self::CATEGORY_LN_CANONICAL_CATEGORY_URL && $answerByFilters == self::ATTRIBUTE_LN_CANONICAL_USE_FILTERS) {
            return true;
        }
        if ($enableByConfig == self::CATEGORY_LN_CANONICAL_USE_FILTERS) {
            return true;
        }
        return false;
    }



    public function isIncludeLNFiltersToCanonicalUrlByFilters()
    {
        $filtersData = $this->getLayeredNavigationFiltersData();

        if (!$filtersData) {
            return 'default';
        }
        usort($filtersData, array($this, "_cmp"));
        foreach ($filtersData as $data) {
            if (!empty($data['use_in_canonical'])) {
                return $data['use_in_canonical'];
            }
        }
        return false;
    }

    protected function _cmp($a, $b)
    {
        $a['position'] = (empty($a['position'])) ? 0 : $a['position'];
        $b['position'] = (empty($b['position'])) ? 0 : $b['position'];

        if ($a['position'] == $b['position']) {
            return 0;
        }
        return ($a['position'] < $b['position']) ? +1 : -1;
    }

    /**
     * @return bool
     */
    public function applyedLayeredNavigationFilters()
    {
        $appliedFilters = Mage::getSingleton('catalog/layer')->getState()->getFilters();
        return (is_array($appliedFilters) && count($appliedFilters) > 0) ? true : false;
    }

    /**
     * Retrive specific filters data as array (use for canonical url)
     * @return array | false
     */
    public function getLayeredNavigationFiltersData()
    {
        $filterData     = array();
        $appliedFilters = Mage::getSingleton('catalog/layer')->getState()->getFilters();

        if (is_array($appliedFilters) && count($appliedFilters) > 0) {
            foreach ($appliedFilters as $item) {

                if (is_null($item->getFilter()->getData('attribute_model'))) {
                    //Ex: If $item->getFilter()->getRequestVar() == 'cat'
                    $use_in_canonical = 0;
                    $position         = 0;
                }
                else {
                    $attributeModel = $item->getFilter()->getAttributeModel();
                    if (is_callable(array($attributeModel, 'getLayeredNavigationCanonical'))) {
                    $use_in_canonical = $attributeModel->getLayeredNavigationCanonical();
                    }else{
                        $use_in_canonical = 0;
                    }

                    if(is_callable(array($attributeModel, 'getPosition'))){
                        $position = $attributeModel->getPosition();
                    }else{
                        $position = false;
                    }
                }

                $filterData[] = array(
                    'name'             => $item->getName(),
                    'label'            => $item->getLabel(),
                    'code'             => $item->getFilter()->getRequestVar(),
                    'use_in_canonical' => $use_in_canonical,
                    'position'         => $position
                );
            }
        }
        return (count($filterData) > 0) ? $filterData : false;
    }
}