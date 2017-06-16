<?php

class Ves_Tabs_Block_Item extends Mage_Catalog_Block_Product_Abstract {

  protected $_config = '';

  public function __construct($attributes=array()) {

    $this->setTemplate('ves/tabs/default/item.phtml');
    parent::__construct();
  }

  public function _toHtml(){
    $config = $this->_config;
    $this->assign('config',$config);
    return parent::_toHtml();
  }

  function setConfigBlock( $config = array() ){
    $this->_config = $config;
    return $this;
  }

  function setConfig($key, $value){
    $this->_config[$key] = $value;
    return $this;
  }

  public function getConfig( $key, $default = '')
  {
    if(isset($this->_config[$key])){
      return $this->_config[$key];
    }else{
      return $default;
    }
  }
}