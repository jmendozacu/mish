<?php


class VES_VendorsRma_Model_Observer
{
    
    /**
     * Auto change state from "Awaiting Other Party's Response" to "Being Reviewed By Admin" when time is expired
     */
    
    
    public function autoChangeState(){
        
        try{
            $resource 			= Mage::getSingleton('core/resource');
            $writeConnection 	= $resource->getConnection('core_write');
            $table 				= $resource->getTableName('vendorsrma/request');
            $now 		= Mage::getModel('core/date')->timestamp();
            $hour =  Mage::helper('vendorsrma/config')->timeStateExpiry()*60*60;  
            $timeMax = $now + $hour;
            $query = "UPDATE `$table` SET state=".VES_VendorsRma_Model_Option_State::STATE_BEING.",updated_at='".Mage::getModel('core/date')->date('Y-m-d H:i:s',$now)."' WHERE updated_at < '".Mage::getModel('core/date')->date('Y-m-d H:i:s',$timeMax)."' AND state = ".VES_VendorsRma_Model_Option_State::STATE_AWAITING;
            $writeConnection->query($query);
        }catch(Mage_Core_Exception $e){
            Mage::log($e->getMessage(),Zend_Log::ERR,'ves_vendor_vendorsrma.log');
        }catch (Exception $e){
            Mage::log($e->getMessage(),Zend_Log::ERR,'ves_vendor_vendorsrma.log');
        }
        
        
        /*
        $now = strtotime(now()) ;
        $hour =  Mage::helper('vendorsrma/config')->timeStateExpiry();
        
        $timeMax = $hour*60*60;
        
        $requests = Mage::getModel("vendorsrma/request")->getCollection()
        ->addAttributeToFilter("state",VES_VendorsRma_Model_Option_State::STATE_AWAITING);
        
        foreach($requests as $request){
            if($now - strtotime($request->getData('updated_at')) > $timeMax){
                $model = Mage::getModel("vendorsrma/request")->load($request->getId());
                $model->setState(VES_VendorsRma_Model_Option_State::STATE_BEING);
                $model->save();
            }
        }
        */
    }
}