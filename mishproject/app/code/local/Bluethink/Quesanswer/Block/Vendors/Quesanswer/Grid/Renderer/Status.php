<?php 

class Bluethink_Quesanswer_Block_Vendors_Quesanswer_Grid_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
public function render(Varien_Object $row) {
 return $this->_getValue($row);
} 
 
protected function _getValue(Varien_Object $row) {
 $val = $row->getData($this->getColumn()->getIndex());
  if($val == 1){
  	echo "Approved";
  }else{
  	echo "Unapproved";
  }
//return $out;
}
}

 

