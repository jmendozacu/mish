<?php
class VES_VendorsMemberShip_Model_Member extends Mage_Core_Model_Abstract
{
    public function checkEmailVendor($email){
        $vendors = Mage::getModel("vendors/vendor")->getCollection()->addAttributeToFilter("email",array("eq"=>$email));
        if(sizeof($vendors) != 0 ) return false;
        return true;
    }
    public function checkVendorId($vendorid){
        $vendors = Mage::getModel("vendors/vendor")->getCollection()->addAttributeToFilter("vendor_id",array("eq"=>$vendorid));
        if(sizeof($vendors) != 0 ) return false;
        return true;
    }

    public function upload($filename){

        $path = Mage::getBaseDir('media') . DS."ves_vendors".DS."logo".DS ;
        $filename = base64_decode($filename);

        $path_tmp =  Mage::getBaseDir('media').DS.'ves_vendors'.DS.'membership'.DS."tmp".DS.$filename;

        $dispretionPath = $this->getDispretionPath($filename);

        $destinationFile = $path.$dispretionPath;
        $this->_createDestinationFolder($destinationFile);

        $filename = $this->getNewFileName($this->_addDirSeparator($destinationFile) . $filename);


        $destinationFile = $this->_addDirSeparator($destinationFile).$filename;

        rename($path_tmp,$destinationFile);

        $filename =  str_replace(DIRECTORY_SEPARATOR, '/',
                $this->_addDirSeparator($dispretionPath)) . $filename;

        $link = "ves_vendors/logo".$filename;
        return $link;
    }

    public function getNewFileName($destFile)
    {
        $fileInfo = pathinfo($destFile);
        if (file_exists($destFile)) {
            $index = 1;
            $baseName = $fileInfo['filename'] . '.' . $fileInfo['extension'];
            while( file_exists($fileInfo['dirname'] . DIRECTORY_SEPARATOR . $baseName) ) {
                $baseName = $fileInfo['filename']. '_' . $index . '.' . $fileInfo['extension'];
                $index ++;
            }
            $destFileName = $baseName;
        } else {
            return $fileInfo['basename'];
        }

        return $destFileName;
    }
    protected function _addDirSeparator($dir)
    {
        if (substr($dir,-1) != DIRECTORY_SEPARATOR) {
            $dir.= DIRECTORY_SEPARATOR;
        }
        return $dir;
    }
    public function getDispretionPath($fileName)
    {
        $char = 0;
        $dispretionPath = '';
        while (($char < 2) && ($char < strlen($fileName))) {
            if (empty($dispretionPath)) {
                $dispretionPath = DIRECTORY_SEPARATOR
                    . ('.' == $fileName[$char] ? '_' : $fileName[$char]);
            } else {
                $dispretionPath = $this->_addDirSeparator($dispretionPath)
                    . ('.' == $fileName[$char] ? '_' : $fileName[$char]);
            }
            $char ++;
        }
        return $dispretionPath;
    }

    public function  _createDestinationFolder($destinationFolder)
    {
        if (!$destinationFolder) {
            return;
        }

        if (substr($destinationFolder, -1) == DIRECTORY_SEPARATOR) {
            $destinationFolder = substr($destinationFolder, 0, -1);
        }

        if (!(@is_dir($destinationFolder) || @mkdir($destinationFolder, 0777, true))) {
            throw new Exception("Unable to create directory '{$destinationFolder}'.");
        }
        return;
    }
}