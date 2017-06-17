<?php 

class Mish_Personallogistic_Block_Adminhtml_Personallogisticorders_Grid_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
public function render(Varien_Object $row) {
 return $this->_getValue($row);
} 
 
protected function _getValue(Varien_Object $row) {
$val = $row->getData($this->getColumn()->getIndex());
$image =  $this->getBaseUrl();

$rest = substr($image,0, -10);  ?>

<img src="<?php echo $rest.'media/personallogistic/profilepic/'.$val; ?>" height="60px" width="60px">

<?php }
}