<?php

class Eadesigndev_Romcity_Block_Adminhtml_Romcity_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId("romcityGrid");
        $this->setDefaultSort("city_id");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel("romcity/romcity")->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn("city_id", array(
            "header" => Mage::helper("romcity")->__("ID"),
            "align" => "right",
            "width" => "50px",
            "type" => "number",
            "index" => "city_id",
        ));

        $this->addColumn('country_id', array(
            'header' => Mage::helper('romcity')->__('Country'),
            'index' => 'country_id',
            'type' => 'options',
            'options' => Eadesigndev_Romcity_Block_Adminhtml_Romcity_Grid::getOptionArray1(),
        ));

        $this->addColumn('region_id', array(
            'header' => Mage::helper('romcity')->__('State'),
            'index' => 'region_id',
            'renderer'=> Eadesigndev_Romcity_Block_Adminhtml_Romcity_Render_Cityname,
//            'type' => 'options',
//            'options' => Eadesigndev_Romcity_Block_Adminhtml_Romcity_Grid::getOptionArray2(),
        ));

        $this->addColumn("cityname", array(
            "header" => Mage::helper("romcity")->__("City"),
            "index" => "cityname",
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('city_id');
        $this->getMassactionBlock()->setFormFieldName('city_ids');
        $this->getMassactionBlock()->setUseSelectAll(true);
        $this->getMassactionBlock()->addItem('remove_romcity', array(
            'label' => Mage::helper('romcity')->__('Remove Romcity'),
            'url' => $this->getUrl('*/adminhtml_romcity/massRemove'),
            'confirm' => Mage::helper('romcity')->__('Are you sure?')
        ));
        return $this;
    }

    static public function getOptionArray1() {
        $data_array = array();        
        $collection = Mage::getSingleton('directory/country')->getResourceCollection()->loadByStore()->toOptionArray(false);
        foreach ($collection as $item) {
            $data_array[$item['value']] = $item['label'];
        }
        return $data_array; 
    }

    static public function getValueArray1() {
        $data_array = array();
        foreach (Eadesigndev_Romcity_Block_Adminhtml_Romcity_Grid::getOptionArray1() as $k => $v) {
            $data_array[] = array('value' => $k, 'label' => $v);
        }
        return($data_array);
    }

    static public function getOptionArray2() {
        $data_array = array();
        $data_array[0] = 'Bihar';
        $data_array[1] = 'Uttar Pradesh';
        $data_array[2] = 'Delhi';
        return($data_array);
    }

    static public function getValueArray2($countryCode) {
        $data_array = array();
        $regionCollection = Mage::getModel('directory/region_api')->items($countryCode);        
        foreach ($regionCollection as $region) {            
            $data_array[] = array('value' => $region['region_id'], 'label' => $region['name']);
        }
        return($data_array);
    }

}
