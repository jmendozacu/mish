<?php
class VES_VendorsLiveChat_ContactController extends Mage_Core_Controller_Front_Action
{
    public function saveAction(){
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('vendorslivechat/contact');
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();
                $result = array('success'=>true,"msg"=>Mage::helper('vendorslivechat')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));

            } catch (Exception $e) {
                $result = array('success'=>false,"msg"=>$e->getMessage());
                return;
            }
        }
        else{
            $result = array('success'=>false,"msg"=>Mage::helper('vendorslivechat')->__('Unable to find item to save'));
        }
        echo json_encode($result);
    }
}