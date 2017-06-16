<?php


class VES_VendorsQuickAddProduct_Vendor_Qap_AjaxController extends VES_Vendors_Controller_Action
{
    public function searchAction() {
        $name = strtolower(trim($this->getRequest()->getPost('keyword')));
        $obj = $this->getRequest()->getPost('obj');
        $collection = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToFilter('name',array('like' => '%'.$name.'%'))
            ->setStoreId(Mage::app()->getStore()->getStoreId())->load();

        $datas = array();
        foreach($collection as $_category) {

            if($parents = $_category->getParentCategories()) {
                usort($parents, array($this, 'cmp'));

                $data = array('label'=>'','value'=>'');
                foreach($parents as $_parent) {
                    $data['label'] .= ($_parent->getId() == end($parents)->getId()) ? $_parent->getName() : $_parent->getName().'&nbsp;&gt;&gt;&nbsp;';
                    $data['value'] .= ($_parent->getId() == end($parents)->getId()) ? $_parent->getId() : $_parent->getId().',';
                }
                $end_cat = Mage::getModel('catalog/category')->load(end($parents)->getId());
                $data['attribute_id'] = $end_cat->getVesAttributeSet();
                $datas[] = $data;
            }
        }

        echo $this->getLayout()->createBlock('core/template')
            ->setInfo($datas)->setTemplate('ves_vendorsqap/widget/form/renderer/fieldset/category/search-result.phtml')->toHtml();
    }


    public function cmp($a, $b)
    {
        if ($a->getLevel() == $b->getLevel()) {
            return 0;
        }
        return ($a->getLevel() < $b->getLevel()) ? -1 : 1;
    }
    
    public function loadChildrenAction(){
        $categoryId = $this->getRequest()->getParam('cat_id');
        $_parent = Mage::getModel('catalog/category')->load($categoryId);
        
        $result = array();
        if($_parent->hasChildren()) {
			$childrenIds = explode(",",$_parent->getChildren());
			$children = Mage::getModel('catalog/category')->getCollection()->addAttributeToFilter('entity_id',array('in'=>$childrenIds))
			->addAttributeToSelect('ves_attribute_set')->load();

        
            foreach($children as $_child) {
                $_child_load = Mage::getModel('catalog/category')->load($_child->getId());
                $result[] = array(
                    'value'             =>      $_child_load->getId(),
                    'attribute_id'      =>      $_child_load->getVesAttributeSet(),
                    'label'             =>      $_child_load->getName(),
                    'level'             =>      $_child_load->getLevel(),
                    'data'              =>      array(),
                    'has_children'      =>      $_child_load->hasChildren(),
                );
            }
        }
        
        $this->getResponse()->setBody(json_encode($result));
    }
}
