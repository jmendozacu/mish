<?php
class VES_Vendors_Block_Template extends Mage_Adminhtml_Block_Template
{
	/**
     * Returns url model class name
     *
     * @return string
     */
    protected function _getUrlModelClass()
    {
        return 'core/url';
    }

    public function getConfigurationUrl(){
		
		return Mage::getUrl('vendors/index/configuration',$this->getRequest()->getParams());
	}

    public function getConfiguration($type=null){
        $vendorAdditionalModel = Mage::getModel('vendors/additional')->getCollection()->addFieldToFilter('vendor_token',$this->getRequest()->getParam('token'))->getFirstItem();
        if($type == 'categories'){
            return array_map('intval',explode(',', $vendorAdditionalModel->getCategories()));
        }

        if($type == 'bank_data'){
            return json_decode($vendorAdditionalModel->getBankData());
        }

    }

    public function getTreeCategories($parentId, $isChild) {
    $allCats = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_active', '1')
//                            ->addAttributeToFilter('include_in_menu', '1')
            ->addAttributeToFilter('level', array('gteq' => 2))
            ->addAttributeToFilter('level', array('lteq' => 3))
            ->addAttributeToFilter('parent_id', array('eq' => $parentId))
            ->addAttributeToSort('position', 'asc');

    $class = (empty($isChild)) ? "sub-cat-list" : "cat-list";
    $activeClass = (in_array($parentId, $this->getConfiguration('categories')) ? " active" : " inactive") ;
    $html .= '<ul class="' . $class . $activeClass .'">';
    foreach ($allCats as $category) {
        $html .= '<li><label for="category_' . $category->getId() . '"><input type="checkbox" name="category_changer[]" value="' . $category->getId() . '" ' . (in_array($category->getId(), $this->getConfiguration('categories')) ? "checked=checked" : "") . ' id="category_' . $category->getId() . '">' . $category->getName() . "</label>";
        $subcats = $category->getChildren();
        if ($subcats != '') {
            $html .= $this->getTreeCategories($category->getId(), false);
        }
        $html .= '</li>';
    }
    $html .= '</ul>';
    return $html;
	}
}
