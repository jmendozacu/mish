<?php
class VES_VendorsRma_Model_Source_File extends Mage_Core_Model_Abstract
{

    
   /**
     * add more Attachment file
     */
    public function addMoreActachment($oldAttactment , $newAttachment){

        if($oldAttactment && explode(",",$oldAttactment)){
            $oldAttactment = explode(",",$oldAttactment);
        }
        else{
            $oldAttactment = array();
        }

        
        foreach($newAttachment as $at){
            array_push($oldAttactment, $at);
        }
  
        return implode(",",$oldAttactment);
    }
    
    /**
     * get Attachment file
     */
    
    public function getActachment($files){
        
        if (!Mage::app()->getStore()->isAdmin()) {
            $session =  Mage::getSingleton('core/session');
        }
        else{
            $session =  Mage::getSingleton('adminhtml/session');
        }
        
        $countfilename = explode(',', $files);
        $attachments=array();
        $check_send_mail=false;
        foreach ($countfilename as $filename){
            if(isset($_FILES['filename'.$filename]['name']) && $_FILES['filename'.$filename]['name'] != '') {
                $ext = pathinfo($_FILES['filename'.$filename]['name'], PATHINFO_EXTENSION);
                $allow_ext=explode(',',Mage::helper('vendorsrma/config')->fileExtension());
                if (in_array(strtolower($ext), $allow_ext)) {
                    array_push($attachments,$_FILES['filename'.$filename]['name']);
                }
                else{
                    $session->addNotice("Disallowed file type.(File name: ".$_FILES['filename'.$filename]['name'].")");
                }
            }
        }
        return $attachments;
    }


    /**
     * upload file
     */

    public function uploadFile($file_ids)
    {
        
        $files = array();
        $file_ids = explode(',', $file_ids);
        foreach ($file_ids as $filename){
            if(isset($_FILES['filename'.$filename]['name']) && $_FILES['filename'.$filename]['name'] != '') {
                try {
                    $uploader = new Varien_File_Uploader('filename'.$filename);
                    $uploader->setAllowedExtensions(explode(',',Mage::helper('vendorsrma/config')->fileExtension()));
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $path = Mage::getBaseDir('media') . DS."ves_vendors".DS."rma".DS."attachment".DS ;
                    //$path = Mage::getBaseDir('media').DS.'ves_vendors'.DS.'rma'.DS.'vendor'.$vendor_id.DS.'request'.$request_id.DS.'message'.$message_id ;
                    $uploader->save($path, $_FILES['filename'.$filename]['name'] );
                    $files[] = "ves_vendors/rma/attachment".$uploader->getUploadedFileName();
                } catch (Exception $e) {
                    //Mage::getSingleton('core/session')->addNotice($e->getMessage());
                }
            }
        }
        return $files;
    }



    /**
     * get Attachment file
     */
    public function getActachmentNote($files){
        
        if (!Mage::app()->getStore()->isAdmin()) {
            $session =  Mage::getSingleton('core/session');
        }
        else{
            $session =  Mage::getSingleton('adminhtml/session');
        }
        
        
        $countfilename = explode(',', $files);
        $attachments=array();
        $check_send_mail=false;
        foreach ($countfilename as $filename){
            if(isset($_FILES['notefilename'.$filename]['name']) && $_FILES['notefilename'.$filename]['name'] != '') {
                $ext = pathinfo($_FILES['notefilename'.$filename]['name'], PATHINFO_EXTENSION);
                $allow_ext=explode(',',Mage::helper('vendorsrma/config')->fileExtension());
                if (in_array(strtolower($ext), $allow_ext)) {
                    array_push($attachments,$_FILES['notefilename'.$filename]['name']);
                }
                else{
                    $session->addNotice("Disallowed file type.(File name: ".$_FILES['filename'.$filename]['name'].")");
                }
            }
        }
        return $attachments;
    }

    /**
     * upload file
     */

    public function uploadFileNoteVendor($file_ids)
    {
        $file_ids = explode(',', $file_ids);
        $files = array();
        foreach ($file_ids as $filename){
            if(isset($_FILES['notefilename'.$filename]['name']) && $_FILES['notefilename'.$filename]['name'] != '') {
                try {
                    $uploader = new Varien_File_Uploader('notefilename'.$filename);
                    $uploader->setAllowedExtensions(explode(',',Mage::helper('vendorsrma/config')->fileExtension()));
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $path = Mage::getBaseDir('media') . DS."ves_vendors".DS."rma".DS."note".DS ;
                    //$path = Mage::getBaseDir('media').DS.'ves_vendors'.DS.'rma'.DS.'vendor'.$vendor_id.DS.'request'.$request_id.DS.'note'.$note_id ;
                    $uploader->save($path, $_FILES['notefilename'.$filename]['name'] );
                    $files[] = "ves_vendors/rma/note".$uploader->getUploadedFileName();
                } catch (Exception $e) {
                   // Mage::getSingleton('vendors/session')->addNotice($e->getMessage());
                }
            }
        }
        return $files;
    }
    /**
     * upload file
     */

    public function uploadFileNote($file_ids)
    {
        $file_ids = explode(',', $file_ids);
        $files = array();
        foreach ($file_ids as $filename){
            if(isset($_FILES['filename'.$filename]['name']) && $_FILES['filename'.$filename]['name'] != '') {
                try {
                    $uploader = new Varien_File_Uploader('filename'.$filename);
                    $uploader->setAllowedExtensions(explode(',',Mage::helper('vendorsrma/config')->fileExtension()));
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $path = Mage::getBaseDir('media') . DS."ves_vendors".DS."rma".DS."note".DS ;
                   // $path = Mage::getBaseDir('media').DS.'ves_vendors'.DS.'rma'.DS.'vendor'.$vendor_id.DS.'request'.$request_id.DS.'note'.$note_id ;
                    $uploader->save($path, $_FILES['filename'.$filename]['name'] );
                    $files[] = "ves_vendors/rma/note".$uploader->getUploadedFileName();
                } catch (Exception $e) {
                   // Mage::getSingleton('core/session')->addNotice($e->getMessage());
                }
            }
        }
        return $files;
    }

}
