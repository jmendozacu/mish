<?php
class OTTO_AdvancedFaq_Block_Adminhtml_Faq_Renderer_Category extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Store{

	public function render(Varien_Object $row)
    {
      	$suppliers	= $row->getData($this->getColumn()->getIndex());
      //	var_dump($suppliers);exit;
        $suppstr="";
            $suppstr="<ul>";
                $productModel = Mage::getModel('advancedfaq/category');
                $attr = $productModel->load($suppliers);
                if ($attr->getId()) {
                $suppstr   .= "<li>".$attr->getData('title')."</li>";
                }
            $suppstr   .= "</ul>";

          return $suppstr;   
       
    }

}