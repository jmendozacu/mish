<?php
/*
* This is the category controller
*/


class Nanowebgroup_HybridMobile_CategoryController extends Mage_Core_Controller_Front_Action
{
    
    public function getJsonAction() {

      $resource = Mage::getSingleton('core/resource');
 
      // Retrieve the read connection
      $readConnection = $resource->getConnection('core_read');

      $query = "SELECT *, COUNT(st.product_id) FROM `catalog_category_flat_store_1` AS `p`
      LEFT JOIN `catalog_category_product` AS `st`
      ON st.category_id = p.entity_id 
      WHERE p.is_active = 1
      GROUP BY p.entity_id";

      $categories = $readConnection->query($query);

      $BaseUrl = Mage::getBaseUrl();
      $MediaUrl = Mage::getBaseUrl('media').'catalog/category/';
      
      $Categories = array();
      foreach ($categories as $key => $value) {
        if($value['parent_id'] == '1' || $value['entity_id'] == '1') {
          continue;
        }

        if($value['image'] != NULL) {
          $image = $MediaUrl. $value['image'];
        } else {
          $image = NULL;
        } 

        if($value['thumbnail'] != NULL) {
          $thumbnail = $MediaUrl. $value['thumbnail'];
        } else {
          $thumbnail = NULL;
        }

        $cat = array(
          "ID" => $value['entity_id'],
          "Name" => $value['name'],
          "Description" => $value['description'],
          "Image" => $image,
          "Thumbnail" => $thumbnail,
          "Product Count" => (int)$value['COUNT(st.product_id)'],
          "URL" => $BaseUrl. $value['url_path'] . "?ios_title=" . trim($value['name']) . "&iphone=true",
          "subcategories" => array()
          );

        $Categories[$value['parent_id']][] = $cat;
      }
      
      $AllCategories = array();
      foreach ($Categories['2'] as $cats) {
        $AllCategories[$cats['ID']] = $cats;
        $AllCategories[$cats['ID']]['subcategories'] = $Categories[$cats['ID']];
      }

      header('Content-type: application/json', TRUE);
      exit(json_encode($AllCategories));

    }
}