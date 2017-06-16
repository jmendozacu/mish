<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * MageWorx DeliveryZone extension
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_Deliveryzone_Block_Adminhtml_Manager_Edit_Tab_Locations extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_element = null;

    public function __construct()
    {
        $this->setTemplate('deliveryzone/locations.phtml');
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }

    public function getElement()
    {
        return $this->_element;
    }

    public function getLocations()
    {
        $countries = Mage::getModel('directory/country')->getCollection()->loadByStore();
        $sort = array();
        $countryRegions = array();
        foreach ($countries as $country) {
            $sort[$country->getName()] = $country->getId();
            $regions = $country->getRegions();
            if (count($regions)) {
                foreach ($regions as $region){
                    $countryRegions[$country->getId()][$region->getId()] = $region->getName();
                    asort($countryRegions[$country->getId()]);
                }
            }
        }

        ksort($sort);
        $countries = array();
        foreach ($sort as $name => $id) {
            $countries[] = array(
               'value'   => $id,
               'label'   => $name,
               'regions' => !empty($countryRegions[$id]) ? $countryRegions[$id] : array()
            );
        }
        return $countries;
    }

    public function getSavedLocations()
    {
        $locations = array();
        if($zoneId = Mage::app()->getRequest()->getParam('id')) {
            $collection = Mage::getResourceModel('deliveryzone/zone_location_collection')->loadByZoneId($zoneId);
            foreach ($collection as $location) {
               $locations[$location->getCountryId()] = array();
               if($location->getRegionIds()) {
                   $locations[$location->getCountryId()] = explode(',', $location->getRegionIds());
               }
            }
        }
        return $locations;
    }
}