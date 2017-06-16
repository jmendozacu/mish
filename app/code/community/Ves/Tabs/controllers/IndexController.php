<?php
class Ves_Tabs_IndexController extends Mage_Core_Controller_Front_Action {

  public function ajaxAction() {
          $json = array();
          $data = $this->getRequest()->getPost();
          $html = '';

           // Check if request have data
          if ( empty($data) || !isset($data['moduleid']) || !isset($data['source_type']) )
          {
            Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl());
            return;
          }

          $config_widget = isset($data['wg'])?$data['wg']:"";
          $attributes = array();
          if($config_widget) {
            $attributes = base64_decode($config_widget);
            $attributes = unserialize($attributes);
          }

          foreach ($attributes as $k => $v) {
            if( $v == 'false' ){
              $attributes[$k] = (bool)false;
            }
            if( $v == 'true' ){
              $attributes[$k] = (bool)true;
            }
          }

          $helper = Mage::helper('ves_tabs/data');
          $config = $helper->get($attributes);
          $config['page'] = isset($data['page'])?$data['page']:1;
          $moduleid = isset($data['moduleid'])?$data['moduleid']:"";
          $time_temp = isset($data['time_temp'])?$data['time_temp']:"";
          $product_id = isset($data['product_id']) ? $data['product_id'] : 0;
          $config['catsid'] = isset($data['catsid'])?(int)$data['catsid']:1;
          $config['is_ajax'] = isset($data['is_ajax'])?(int)$data['is_ajax']:1;
          $json['row'] = $json['rows'] = $json['productslist'] = '';
          $json['moduleid'] = $moduleid;
          $json['blockid'] = $data['blockid'];
          $json['catsid'] = $data['catsid'];

          switch ($config['ajax_type']) {
            case 'carousel':
            case 'sub-category':
              $blockMainConfig = $config;
              $blockMainConfig['itemspage'] = $blockMainConfig['limit_item'];
              $list = Mage::getModel('ves_tabs/product')->getListProducts($blockMainConfig);
              $blockMainConfig['itemspage'] = $config['itemspage'];
              $json['rows'] = Mage::helper('ves_tabs/data')->getAjaxData($blockMainConfig, $list);
              break;
            case 'append':
               $blockMainConfig = $config;
               $blockChildConfig = $config;

                // Return HTML of Main Block
               $blockMainConfig['itemspage'] = $blockMainConfig['limit_item'];
               $list = Mage::getModel('ves_tabs/product')->getListProducts($blockMainConfig);
               $blockMainConfig['itemspage'] = $config['itemspage'];
               $json['rows'] = Mage::helper('ves_tabs/data')->getAjaxData($blockMainConfig, $list);

               // Return HTML of Child Block
               if( !$config['enable_tab'] ){
                $blockChildConfig['itemsrow'] = 1;
                $blockChildConfig['show_btn'] = $blockChildConfig['enable_quickview'] = $blockChildConfig['enable_swap'] = $blockChildConfig['enable_sale_icon'] = $blockChildConfig['enable_new_icon'] = $blockChildConfig['show_review'] = $blockChildConfig['show_wc'] = 0;
              }

              $blockChildConfig['mini'] = 1;
              $blockChildConfig['itemspage'] = $blockChildConfig['source_limit_item'];
              $listChildBlock = Mage::getModel('ves_tabs/product')->getListProducts($blockChildConfig);
              $blockChildConfig['itemspage'] = $config['itemspage'];
              $blockChildConfig['image_width'] = $config['thumbwidth_childblock'];
              $blockChildConfig['image_height'] = $config['thumbheight_childblock'];
              $json['productslist'] = Mage::helper('ves_tabs/data')->getAjaxData($blockChildConfig, $listChildBlock);
              break;
            case 'loadmore':
                $list = Mage::getModel('ves_tabs/product')->getListProducts($config);

                if($list['hasNextData']){
                  $json['hasNextData'] = 1;
                }else{
                  $json['hasNextData'] = 0;
                }

                $itemsrow = $config['itemsrow'] -  (( ($config['page']-1) * $config['itemspage']) % $config['itemsrow']);

                if( $itemsrow % $config['itemsrow'] == 0 ){ $itemsrow = 0; }

                $number_item = $config['itemspage'];
                $lastPage = ceil( $config['limit_item'] / $config['itemspage'] );

                if(  $config['page'] == $lastPage ){
                  $number_item = $config['limit_item'] - ( ($config['page']-1) * $config['itemspage']);
                }

                $x = $count = 0;
                $loadScript = true;
                $size = count($list['products']);
                $i = 1;

                foreach ( $list['products'] as $product ) {
                    if( $count == $size - 1 || $count == $number_item - 1 && $config['ajax_type'] == 'loadmore'){
                      $loadScript = false;
                    }
                    if( $x < $itemsrow && $config['page'] > 1 ){
                      $x++;
                      $json['row'] .= Mage::app()
                      ->getLayout()
                      ->createBlock("ves_tabs/item")
                      ->setConfigBlock($config)
                      ->assign('i', $x+$config['itemspage'])
                      ->assign('product',$product)
                      ->assign('config', $config)
                      ->assign('loadScript',$loadScript)
                      ->toHtml();
                    }else{
                     if( ($i-1) % $config['itemspage'] == 0 && $i>1 && ( $config['ajax_type'] == 'carousel' || $config['ajax_type'] == 'append' ) ){
                      $config['page'] = $config['page'] + 1;
                      $i = 1;
                    }

                    $json['rows'] .= Mage::app()
                    ->getLayout()
                    ->createBlock("ves_tabs/item")
                    ->setConfigBlock($config)
                    ->assign('i', $i)
                    ->assign('config', $config)
                    ->assign('product',$product)
                    ->assign('loadScript',$loadScript)
                    ->toHtml();
                    $i ++;
                  }
                  if( $count == $number_item - 1 && $config['ajax_type'] == 'loadmore' ){ break; }
                  $count++;
                }
              break;
          }
      echo Mage::helper('core')->jsonEncode( $json );
    }
}