<?php

class RLTS_Toplinks_Block_Navigation extends Mage_Customer_Block_Account_Navigation{
    
    public function removeLinkByName($name){
        $all_links = $this->_links;
        foreach($all_links as $key=>$value){
            if($name==$value->getName()){
                unset($this->_links[$key]);
            }
        }
        return $this;
    }
}
