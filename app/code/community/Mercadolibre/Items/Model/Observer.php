<?php

class Mercadolibre_Items_Model_Observer
{
    public function getMLCatergoriesAllData($observer)
    {
		Mage::getModel('items/melicategories')->getMLCatergoriesAllData();
		echo "All categories data have been imported successfully.<br /><br />";
    }

    public function getMLCategoryAttributes($observer)
	{
	  Mage::getModel('items/melicategories')->getMLCategoryAttributes();

    }
   
   public function getCleanUpLog($observer){
   		 Mage::getModel('items/common')->getCleanUpLog();
   }
   
    public function getMLInventoryPriceUpdate($observer){
   		 Mage::getModel('items/common')->getMLInventoryPriceUpdate();
   }
     
   public function costomMLActionOnSaveConfig($observer){
   		 Mage::getModel('items/common')->createNotificationApplication();
		 //Mage::getModel('items/melicategories')-> getMLCatergoriesWithFilter();
   }
   
   
   
}