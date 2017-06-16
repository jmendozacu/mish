<?php
class VES_VendorsAttributeOptions_Vendor_Options_AjaxController extends VES_Vendors_Controller_Action {
    public function saveAction() {
        $post       = $this->getRequest()->getPost();
        $form_key   = $post['form_key'];
        $product    = $post['product'];
        //$_attribute_id = $post['attribute_id'];


        $option = array();
       // $option['attribute_id'] = $_attribute_id;

        foreach($product as $_attribute_code => $_data) {
            if(!isset($_attribute_id)) {
                $model = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', $_attribute_code);
                $_attribute_id = $model->getAttributeId();
                $option['attribute_id'] = $_attribute_id;
            }
            if($_data) {
                foreach($_data as $_id => $_info) {
                    if(is_array($_info)) {
                        $option['value'][$_id][0] = $_info['title'];
                        $option['order'][$_id] = $_info['sort_order'];
                    }
                    else {continue;}
                }
            } else {continue;}
        }

        $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
        $setup->addAttributeOption($option);

        //return all options

        $result = array();
        if($model->usesSource()) {
            $allOptions = $model->getSource()->getAllOptions();
            foreach ($allOptions as $instance) {
                if($instance['value'] == '') continue;  //bo qua option null
                //$result[$instance['value']] = $instance['label'];
                $result['select_json'][] = array('value' => $instance['value'], 'label' => $instance['label']);
            }
        }

        //set attribute_value


        echo json_encode($result);
    }
}