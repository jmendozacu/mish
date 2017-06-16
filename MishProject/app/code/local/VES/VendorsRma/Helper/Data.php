<?php

class VES_VendorsRma_Helper_Data extends Mage_Core_Helper_Abstract
{
    const QTY_APECPT = 0;
    const EXEPTION_QTY_ZERO = 1;
    const EXEPTION_QTY_MUCH = 2;
    /** validate qty item rma */

    public function validateQtyOrder($items,$status,$isEdit){
       // echo $isEdit;exit;
        $check = false;
        if(!is_array($items) || sizeof($items) == 0) return self::EXEPTION_QTY_ZERO;
        foreach($items as $id=>$qty){
            if($qty != 0){
                $check = true;
                $item = Mage::getModel('sales/order_item')->load($id);
                $models = Mage::getModel("vendorsrma/item")->getCollection()->addFieldToFilter("order_item_id",$id);
                $qty_rma_old = 0;
                $rmas = null;
                $options = Mage::getModel("vendorsrma/status")->getOptions();
                foreach($models as $rma_item){
                    $request = Mage::getModel("vendorsrma/request")->load($rma_item->getRequestId());
                    if($request->getData("status") == $options[3]["value"]) continue;
                    if($isEdit && $request->getId() == $isEdit) continue;
                    /* check request not complete */
                    $qty_rma_old +=   $rma_item->getQty();

                    $rmas[] = array($request->getId() => $request->getData("increment_id"));
                }
                
                if($status ==  Mage_Sales_Model_Order::STATE_COMPLETE){ 
                    if($qty_rma_old == 0){
                        $qty_olds = $item->getData("qty_invoiced") - $item->getData("qty_refunded") > 0 ? $item->getData("qty_invoiced") - $item->getData("qty_refunded") : 0;
    
                    }
                    else{
                        $qty_olds = $item->getData("qty_invoiced") - $item->getData("qty_refunded") - $qty_rma_old > 0 ? $item->getData("qty_invoiced") - $item->getData("qty_refunded") - $qty_rma_old : 0;
                    }
                }
                else{
                    if($qty_rma_old == 0){
                        $qty_olds = $item->getData("qty_invoiced") > 0 ? $item->getData("qty_invoiced")  : 0;
                    
                    }
                    else{
                        $qty_olds = $item->getData("qty_invoiced") - $qty_rma_old > 0 ? $item->getData("qty_invoiced") - $qty_rma_old : 0;
                    }
                }
                
                $qty_olds = (int)$qty_olds;
                
                if(!Mage::helper("vendorsrma/config")->allowPerOrder()){
                    if($qty != $qty_olds) return self::EXEPTION_QTY_MUCH;
                }

                if($qty > $qty_olds) return self::EXEPTION_QTY_MUCH;
            }
        }

       // echo $qty_olds;exit;
        if($check) return self::QTY_APECPT;
        return self::EXEPTION_QTY_ZERO;
    }

    /**
     * message word limit
     *
     * @return Ambigous <mixed, string, NULL, multitype:, multitype:Ambigous <string, multitype:, NULL> >
     */
    function wordLimiter($str,$maxlen=100,$limit=15,$end_char = '&#8230;'){
        $str = strip_tags($str);
        if (trim($str) == ''){
            return $str;
        }
        if(strlen($str) > $maxlen ) $str=substr($str, 0,$maxlen).$end_char;
        //if(strlen( $str ) > $limit ){
        preg_match('/^\s*+(?:\S++\s*+){1,'.(int) $limit.'}/', $str, $matches);

        if (strlen($str) == strlen($matches[0])){
            $end_char = '';
        }

        $text=trim(strip_tags($matches[0],'<blockquote>')).$end_char;
        $text=preg_split('/(<blockquote?>|<\/blockquote>)/i', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        //}
        return $text[0];
    }

    /**
     * check html message
     *
     * @return Ambigous <mixed, string, NULL, multitype:, multitype:Ambigous <string, multitype:, NULL> >
     */
    public function isHtmlMessage($message){
        return ($message != strip_tags($message));
    }


    public function getClassIcon($file){
        $class= "";
        $path_attachment=pathinfo($file);
        switch($path_attachment['extension']){
            case 'jpg' :
                $class = "icon-jpg";
                break;
            case 'JPG' :
                $class = "icon-jpg";
                break;
            case 'jpeg' :
                $class = "icon-jpeg";
                break;
            case 'JPEG' :
                $class = "icon-jpeg";
                break;
            case 'png' :
                $class = "icon-png";
                break;
            case 'PNG' :
                $class = "icon-png";
                break;
            case 'gif' :
                $class = "icon-gif";
                break;
            case 'GIF' :
                $class = "icon-gif";
                break;
            case 'pdf' :
                $class = "icon-pdf";
                break;
            case 'PDF' :
                $class = "icon-pdf";
                break;
            case 'zip' :
                $class = "icon-zip";
                break;
            case 'ZIP' :
                $class = "icon-zip";
                break;
            case 'rar' :
                $class = "icon-rar";
                break;
            case 'RAR' :
                $class = "icon-rar";
                break;
            case 'txt' :
                $class = "icon-txt";
                break;
            case 'TXT' :
                $class = "icon-txt";
                break;
            case 'csv' :
                $class = "icon-csv";
                break;
            case 'CSV' :
                $class = "icon-csv";
                break;
            case 'doc' :
                $class = "icon-doc";
                break;
            case 'DOC' :
                $class = "icon-doc";
                break;
            case 'docx' :
                $class = "icon-doc";
                break;
            case 'DOCX' :
                $class = "icon-doc";
                break;
            default :
                $class = "icon-default";
                break;
        }
        return $class;
    }

}