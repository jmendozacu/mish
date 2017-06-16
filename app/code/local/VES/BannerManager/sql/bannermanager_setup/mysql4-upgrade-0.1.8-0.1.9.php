<?php


$collections = Mage::getModel('bannermanager/banner')->getCollection();


$data_banner = array(
    'template'=> 1,
    'controls_type' => "number",
    'duration' => "0.5",
    'frequency' => "4.0",
    'autoglide' => 1,
    'easing'=>'scroll',
    'nivo_options'=>'a:11:{s:6:"effect";s:6:"random";s:5:"theme";s:7:"default";s:6:"slices";s:2:"15";s:7:"boxCols";s:1:"8";s:7:"boxRows";s:1:"4";s:9:"animSpeed";s:3:"500";s:9:"pauseTime";s:4:"3000";s:12:"directionNav";s:4:"true";s:10:"controlNav";s:4:"true";s:12:"pauseOnHover";s:4:"true";s:13:"manualAdvance";s:5:"false";}',
);

foreach($collections as $banner){
    $model = Mage::getModel('bannermanager/banner');
    $model->setData($data_banner)->setId($banner->getId());
    $model->save();
}



$items = Mage::getModel('bannermanager/item')->getCollection();


$data_item = array(
    'target_mode'=>0,
    'desc_pos'=>3,
    'background'=>1,
);

foreach($items as $item){
    $model = Mage::getModel('bannermanager/item');
    $model->setData($data_item)->setId($item->getId());;
    $model->save();
}
