<?php

class VES_VendorsRma_DownloadController extends Mage_Core_Controller_Front_Action
{
    /** down load attrachmment Message */
    public function indexAction(){
        $file=base64_decode($this->getRequest()->getParam('file'));
        $path = Mage::getBaseDir('media').DS.$file ;
      //  echo $path;exit;
        if (! is_file ( $path ) || ! is_readable ( $path )) {
            throw new Exception ( );
        }
        $characters= array(' ','/','*','?','<','>');
        $name_file = str_replace($characters, '_', basename($path));
        $name_file =  preg_replace('/\s+/', '_', $name_file);
        $name_file = trim($name_file,'_');
        $content = file_get_contents($path);
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$name_file);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', 'application/force-download');
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    /** down load attrachmment Message */
    public function noteAction(){
        $file=base64_decode($this->getRequest()->getParam('file'));
        $path = Mage::getBaseDir('media').DS.$file ;
       
        if (! is_file ( $path ) || ! is_readable ( $path )) {
            throw new Exception ( );
        }
        $characters= array(' ','/','*','?','<','>');
        $name_file = str_replace($characters, '_', basename($path));
        $name_file =  preg_replace('/\s+/', '_', $name_file);
        $name_file = trim($name_file,'_');
        $content = file_get_contents($path);
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$name_file);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', 'application/force-download');
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}