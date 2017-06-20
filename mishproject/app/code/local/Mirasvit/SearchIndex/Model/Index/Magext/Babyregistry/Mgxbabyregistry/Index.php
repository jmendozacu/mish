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
 * @package   Sphinx Search Ultimate
 * @version   2.3.1
 * @revision  640
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_SearchIndex_Model_Index_Magext_Babyregistry_Mgxbabyregistry_Index extends Mirasvit_SearchIndex_Model_Index
{
    public function getBaseGroup()
    {
        return 'MagExt';
    }

    public function getBaseTitle()
    {
        return 'Baby Registries';
    }

    public function canUse()
    {
        return Mage::getConfig()->getModuleConfig('MagExt_BabyRegistry')->is('active', 'true');
    }

    public function getPrimaryKey()
    {
        return 'registry_id';
    }

    public function getAvailableAttributes()
    {
         $result = array(
            'babyregistry_id'               => Mage::helper('searchindex')->__('Baby Registry Id'),
            'firstname'                     => Mage::helper('searchindex')->__('First name'),
            'lastname'                      => Mage::helper('searchindex')->__('Last name'),
            'co_lastname'                   => Mage::helper('searchindex')->__('Co-Last name'),
        );

        return $result;
    }

    public function getCollection()
    {
        $collection = Mage::getModel('mgxbabyregistry/mgxbabyregistry')->getCollection();
        $collection->getSelect()->where('`active` = 0');

        $this->joinMatched($collection, 'main_table.registry_id');

        return $collection;
    }
}