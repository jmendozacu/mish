<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alesioo
 * Date: 12.12.12
 * Time: 16:24
 * To change this template use File | Settings | File Templates.
 */


class Etheme_Welldone_Helper_Data extends Mage_Core_Helper_Abstract
{
    public $theme;

    function __construct()
    {
        $this->theme='welldone';
    }

    public function fileLoad($name, &$formData, $pathModule)
    {
        if (isset($_FILES[$name]['name']) && $_FILES[$name]['name'] != null)
        {
            $fileUploader = new Varien_File_Uploader($name);
            $fileUploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $fileUploader->setAllowRenameFiles(false);
            $fileUploader->setFilesDispersion(false);
            //$path = Mage::getBaseDir() . DS.$pathModule.DS ;
            $path = 'skin' . DS . 'adminhtml' . DS . 'base' . DS . 'default' . DS . 'coolbaby' . DS . 'images' . DS . 'Configsets' . DS;
            $fileUploader->save($path, $_FILES[$name]['name']);
            $formData[$name] = $pathModule . DS . $_FILES[$name]['name'];
        }
    }

    //loop through folders and sub folders with option to remove specific files.
    public function listFolderFiles($dir, $exclude)
    {
        $ffs = scandir($dir);
        echo '<ul class="ulli">';
        foreach ($ffs as $ff) {
            if (is_array($exclude) and !in_array($ff, $exclude)
            ) {
                if ($ff != '.' && $ff != '..') {
                    if (!is_dir($dir . '/' . $ff)) {
                        echo '<li><a href="edit_page.php?path=' . ltrim($dir . '/' . $ff, './') . '">' . $ff . '</a>';
                    } else {
                        echo '<li>' . $ff;
                    }
                    if (is_dir($dir . '/' . $ff)) $this->listFolderFiles($dir . '/' . $ff, $exclude);
                    echo '</li>';
                }
            }
        }
        $listDir = array();
        echo '</ul>';
    }


    //Return an array of file names and folders in directory:
    function ReadFolderDirectory($dir = "root_dir/here")
    {
        if ($handler = opendir($dir)) {
            while (($sub = readdir($handler)) !== FALSE) {
                if ($sub != "." && $sub != ".." && $sub != "Thumb.db") {
                    if (is_file($dir . "/" . $sub)) {
                        $listDir[] = $sub;
                    } elseif (is_dir($dir . "/" . $sub)) {
                        $listDir[$sub] = $this->ReadFolderDirectory($dir . "/" . $sub);
                    }
                }
            }
            closedir($handler);
        }
        return $listDir;
    }

    //view files by extension

    public function listFolderFiles_by_ext($dir, $type)
    {
        $dir = '.\\' . $dir . '\\'; // reminder: escape your slashes
        $filetype = "*." . $type;
        $filelist = shell_exec("dir {$dir}{$filetype} /a-d /b");
        $file_arr = explode("\n", $filelist);
        array_pop($file_arr); // last line is always blank
        return $file_arr;
    }


    public function cropResizeImg($fileName, $tmp, $width, $height = '')
    {
        $folderURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        $imageURL = $folderURL . $fileName;

        $basePath = $tmp;
        $newPath = Mage::getBaseDir() . DS . 'skin' . DS . 'adminhtml' . DS . 'base' . DS . 'default' . DS . 'coolbaby' . DS . 'images' . DS . 'Configsets' . DS . $fileName;

        if (is_file($tmp)) {
            $imageObj = new Varien_Image($basePath);
            $imageObj->constrainOnly(TRUE);
            $imageObj->keepAspectRatio(FALSE);
            $imageObj->keepFrame(FALSE);

            $currentRatio = $imageObj->getOriginalWidth() / $imageObj->getOriginalHeight();
            $targetRatio = $width / $height;

            if ($targetRatio > $currentRatio) {
                $imageObj->resize($width, null);
            } else {
                $imageObj->resize(null, $height);
            }

            $diffWidth = $imageObj->getOriginalWidth() - $width;
            $diffHeight = $imageObj->getOriginalHeight() - $height;


            //$imageObj->resize($width, $height);
            $imageObj->crop(
                floor($diffHeight * 0.5),
                floor($diffWidth / 2),
                ceil($diffWidth / 2),
                ceil($diffHeight * 0.5)
            );

            $imageObj->save($newPath);
        }

        return $fileName;
    }



    public function ratingStars($tpl,$tpl2,$_product,$el)
    {
        if(!$this->getConfigOption('products/show_rating')) return;
        /**
         * Getting reviews collection object
         */
        $productId = $_product->getId();
        $reviews = Mage::getModel('review/review')
            ->getResourceCollection()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addEntityFilter('product', $productId)
            ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
            ->setDateOrder()
            ->addRateVotes();
        /**
         * Getting average of ratings/reviews
         */
        $stars = 0;
        $avg = 0;
        $output = '';
        $ratings = array();
        if (count($reviews) > 0) {
            foreach ($reviews->getItems() as $review) {
                foreach ($review->getRatingVotes() as $vote) {
                    $ratings[] = $vote->getPercent();
                }
            }
            $crat=count($ratings);
            if($crat==0)$crat=1;
            $avg = array_sum($ratings) / $crat;
            $stars = round($avg * 5 / 100);
        }

        if ($stars) {
            for ($i = 0; $i < $stars; $i++) $output .= $tpl;
            for ($i = 0; $i < (5 - $stars); $i++) $output .= $tpl2;
        }

        return $output;
    }


    public function rolloverImage($_product,$width,$height,$attr,$el)
    {
        if(!Mage::getStoreConfig('welldone/options/image_rollover_mode'))return;
        $_product->load('media_gallery');
        if ($temp = $_product->getMediaGalleryImages())
        {
            if ($_image = $temp->getItemByColumnValue('position', Mage::getStoreConfig('welldone/options/image_rollover_sort')))
            {
                return '<img src="'.$el->helper('catalog/image')->init($_product, 'small_image',$_image->getFile())->resize($width, $height) . '" '.$attr.' data-image2x="' . $el->helper('catalog/image')->init($_product, 'small_image',$_image->getFile())->resize($width * 2, $height * 2) . '"    alt="' . $el->stripTags($_product->getName(), null, true) . '">';

            }
        }

    }

    public function getFeaturedImage($_product,$width,$height,$attr,$el)
    {
        $_product->load('media_gallery');
        if ($temp = $_product->getMediaGalleryImages())
        {
            if ($_image = $temp->getItemByColumnValue('label', 'featured'))
            {
                return '<img src="'.$el->helper('catalog/image')->init($_product, 'small_image',$_image->getFile())->resize($width, $height) . '" '.$attr.' data-image2x="' . $el->helper('catalog/image')->init($_product, 'small_image',$_image->getFile())->resize($width * 2, $height * 2) . '"    alt="' . $el->stripTags($_product->getName(), null, true) . '">';
            } else
            {
                return '<img src="'.$el->helper('catalog/image')->init($_product, 'small_image')->resize($width,$height). '" '.$attr.' data-image2x="' . $el->helper('catalog/image')->init($_product, 'small_image')->resize($width * 2, $height * 2) . '"    alt="' . $el->stripTags($_product->getName(), null, true) . '">';
            }
        }
    }

    public function showRollover($_product)
    {
        if(!Mage::getStoreConfig('welldone/options/image_rollover_mode'))return;
        $_product->load('media_gallery');
        if ($temp = $_product->getMediaGalleryImages())
        {
            if ($_image = $temp->getItemByColumnValue('position', Mage::getStoreConfig('welldone/options/image_rollover_sort')))
            {
                return true;
            }
        }
    }

    function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array($r, $g, $b);
        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }

    function getProductLabel($_product,$el)
    {
        $html = array();
        $now = date("Y-m-d");
        $specialFrom = substr($_product->getData('special_from_date'), 0, 10);
        $specialTo = substr($_product->getData('special_to_date'), 0, 10);
        $special = false;
        if (!empty($specialFrom) && !empty($specialTo)) {
            if ($now >= $specialFrom && $now <= $specialTo) $special = true;

        } elseif (!empty($specialFrom) && empty($specialTo)) {
            if ($now >= $specialFrom) $special = true;

        } elseif (empty($specialFrom) && !empty($specialTo)) {
            if ($now <= $specialTo) $special = true;
        }
        if ($this->getConfigOption('products/show_sale_label')) {
            if ($special) $html[] = ' <div class="product-preview__label product-preview__label--right product-preview__label--sale"><span>'.$el->__('Sale').$this->outputDiscountLabel($_product).'</span></div>';
        }
        if ($this->getConfigOption('products/show_new_label')) {
            $now = date("Y-m-d");
            $newsFrom = substr($_product->getData('news_from_date'), 0, 10);
            $newsTo = substr($_product->getData('news_to_date'), 0, 10);
            $new = false;
            if (!empty($newsFrom) && !empty($newsTo)) {
                if ($now >= $newsFrom && $now <= $newsTo) $new = true;

            } elseif (!empty($newsFrom) && empty($newsTo)) {
                if ($now >= $newsFrom) $new = true;

            } elseif (empty($newsFrom) && !empty($newsTo)) {
                if ($now <= $newsTo) $new = true;
            }
            if ($new) $html[] ='<div class="product-preview__label product-preview__label--left product-preview__label--new"><span>'.$el->__('New').'</span></div>';
        }
        $html = implode("\n", $html);
        return $html;
    }



    public function outputDiscountLabel($_product)
    {
        if($this->getConfigOption('general/catalog_mode'))return;
        if (!($_product->type_id == 'grouped' || $_product->type_id == 'bundle')) {

            if(!$this->getConfigOption('products/discount_label'))return;
            if ($_product->type_id != 'grouped')
                $price_new = $_product->getFinalPrice();
            else
                $price_new = $_product->min_price;

            $price_old = $_product->getPrice();

            if($price_old==0)$price_old=1;
            $discount=round((($price_new-$price_old)*100)/$price_old);

            if($discount!=0)
            return '<br>-'.$discount.'%';
            else return '';
        } else return '';

    }

    public function quickViewLink($tpl,$_product,$el)
    {
        if(!Mage::getStoreConfig('welldone/products/quick_view'))return;
        return str_replace('href="#"','title="'.$el->__('Quick View').'" href="'.$el->getUrl('ajax/index/options', array('product_id' => $_product->getId(), '_secure' => Mage::app()->getFrontController()->getRequest()->isSecure())).'"',$tpl);
    }

    function replace_uri($str) {
        $pattern = '#(^|[^\"=]{1})(http://|ftp://|mailto:|news:)([^\s<>]+)([\s\n<>]|$)#sm';
        return preg_replace($pattern,"\\1<a target=\"_blank\" href=\"\\2\\3\">\\2\\3</a>\\4",$str);
    }

    public function getHomeLink() {
        return array(
            "title" => $this->__("Home Page"),
            "label" => $this->__('Home'),
            "link"  => Mage::getUrl()
        );
    }


    function refreshCssFiles($store, $website)
    {
        /*3 ways to refresh CSS
         * DEFAULT-WEBSITES-STORES  (All available stores)
         * WEBSITES-STORES (stores from choosen website)
         * STORE (choosen store)
         * */
        if(!$website)
        {
            //refresh all Websites css
            foreach(Mage::app()->getWebsites() as $website)
            {
                $this->refreshWebsiteStores($website);
            }
        }
        else
        {
            if($store)
            {
                //refresh Store css
                $this->refreshStoreCss($store);
            }
            else
            {
                //refresh Website css
                $this->refreshWebsiteStores($website);
            }
        }
    }

    function refreshStoreCss($store)
    {
        Mage::register('store_for_css', $store);
        $path = Mage::getBaseDir() . '/' . 'skin/frontend/welldone/default/css/colors/';

        $prefix = 'colors_';
        if ($store) {
            $filename = $store;
        }

        $path_full = $path . $prefix . $filename . '.css';


        /*
         * how get frontend phtml output http://stackoverflow.com/questions/12290938/get-frontend-phtml-templates-output-inside-a-model-method-in-magento
         * */
        $css_output = Mage::app()->getLayout()->createBlock('core/template')->setData('area', 'frontend')->setTemplate('etheme/welldone/cssrefresh/colors.phtml')->toHtml();

        /*
         * write to file described here  http://inchoo.net/ecommerce/magento/magento-code-library/
         * */
        try {
            if(file_exists($path_full))unlink($path_full);
            $flocal = new Varien_Io_File();
            $flocal->open(array('path' => $path));
            $flocal->streamOpen($path_full, 'w+');
            $flocal->streamWrite($css_output);
            $flocal->streamClose();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        Mage::unregister('store_for_css');
    }

    function refreshWebsiteStores($website)
    {
        foreach(Mage::app()->getWebsite($website)->getStoreCodes() as $store)$this->refreshStoreCss($store);
    }

    public function getIsHomePage()
    {
        $page = Mage::app()->getFrontController()->getRequest()->getRouteName();
        $homePage = false;

        if($page =='cms'){
            $cmsSingletonIdentifier = Mage::getSingleton('cms/page')->getIdentifier();
            $homeIdentifier = Mage::app()->getStore()->getConfig('web/default/cms_home_page');
            if($cmsSingletonIdentifier === $homeIdentifier){
                $homePage = true;
            }
        }

        return $homePage;
    }

    public function ifSearchResultPage()
    {
        $pageIdentifier = Mage::app()->getFrontController()->getAction()->getFullActionName();
        return ($pageIdentifier=='catalogsearch_result_index' || $pageIdentifier=='catalogsearch_advanced_result');
    }

    function countdownSpecialPrice($_product,$selector,$el,$in_list_mode=false)
    {
        if(!$_product->isSaleable())return;
        $output='';
        $specialTo = substr($_product->getData('special_to_date'), 0, 10);
        $now = date("Y-m-d");
        if (!empty($specialTo) && $this->getConfigOption('products/countdown')) {
            if ($now < $specialTo)
            {
                $to_year=substr($_product->getData('special_to_date'), 0, 4);
                $to_month=substr($_product->getData('special_to_date'), 5, 2);
                $to_day=substr($_product->getData('special_to_date'), 8, 2);
                $output.='<div class="countdown_box">
                    <div class="countdown_inner">
                        <div class="title">'.$el->__('Special price valid:').'</div>
                        <div class="'.$selector.'-'.$_product->getId().' hasCountdown"></div>
                      </div>
                    </div>
                    <script type="text/javascript">
                    jQuery(function () {
                        jQuery(".'.$selector.'-'.$_product->getId().'").countdown({until: new Date('.$to_year.','.($to_month-1).', '.$to_day.')});
                    });
                   </script>
                ';
            }
        }
        return $output;
    }

    function getConfigOption($optionString, $storeId = NULL)
    {
        if ($storeId)
            return Mage::getStoreConfig($this->theme.'/' . $optionString, $storeId);
        else
            return Mage::getStoreConfig($this->theme.'/' . $optionString);

    }

    function getLayoutOption($optionString,$storeId=NULL)
    {
        if ($storeId)
            return Mage::getStoreConfig($this->theme.'layout/' . $optionString, $storeId);
        else
            return Mage::getStoreConfig($this->theme.'layout/' . $optionString);
    }

    function getColorOption($optionString,$storeId=NULL)
    {
        if ($storeId)
            return Mage::getStoreConfig($this->theme.'colors/' . $optionString, $storeId);
        else
            return Mage::getStoreConfig($this->theme.'colors/' . $optionString);
    }
}