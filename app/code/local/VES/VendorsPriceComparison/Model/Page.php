<?php
class VES_VendorsPriceComparison_Model_Page extends Mage_Core_Model_Abstract
{
    protected $_total;
    protected $_link;
    protected $_pages;
    protected $_page;
    protected $_displaylink = 5;
    public function __construct($total=null){
        $this->_total=$total;
    }
    public function findTotal($total){
        $this ->_total = $total;
    }
    public function setLink($link){
        $this->_link=$link;
    }
    public function setDisplayLink($count){
        $this->_displaylink=$count;
    }
    public function findPages($limit){
        $this->_pages = ceil($this->_total / $limit);
    }
    public function setPage($page){
        $this->_page=$page;
    }
    public function rowStart($limit){
        return (!isset($this->_page)) ? 0 :  ($this->_page-1) * $limit;
    }
    public function pagesList($curpage){
        $total = $this->_total;
        $pages = $this->_pages;
        if($pages <=1){return '';}
        $page_list="<ul class='page-link'>";
        if($curpage!=1){
           // $page_list .= '<li class="button-page"><a href="'.$this->_link.'?page=1" title="First" class="page">First </a></li>';
            $page_list .= '<li class="button-page"><a href="javascript:void(0)" onclick="Comparison.changePage(1)" title="First" class="page">First </a></li>';
        }
        if($curpage  > 1){
            //$page_list .= '<li class="button-page"><a href="'.$this->_link.'?page='.($curpage-1).'" title="Preview" class="page">Back</a></li>';
            $page_list .= '<li class="button-page"><a href="javascript:void(0)" onclick="Comparison.changePage('.($curpage-1).')"  title="Preview" class="page">Back</a></li>';
        }
        if($curpage <= $this->_displaylink -2 ){
            for($i=1; ($i<= $this->_displaylink) and ($i<=$pages); $i++){
                if($i == $curpage){
                    //$page_list .= "<li><a onlick='javascript:void(0)' class='page active'>".$i."</a></li>";
                    $page_list .= "<li><a onlick='javascript:void(0)' class='page active'>".$i."</a></li>";
                }
                else{
                 //   $page_list .= '<li><a href="'.$this->_link.'?page='.$i.'" title="Page '.$i.'" class="page">'.$i.'</a></li>';
                    $page_list .= '<li><a href="javascript:void(0)" onclick="Comparison.changePage('.$i.')" title="Page '.$i.'" class="page">'.$i.'</a></li>';
                }
                $page_list .= " ";
            }
            if($pages > $this->_displaylink) $page_list .= "<li>...</li>";
        }
        else {if($pages >= $curpage + 2){
            $page_list .= "<li>...</li>";
            for($i=$curpage-2; ($i <=$curpage+2) and ($i <= $pages); $i++){
                if($i == $curpage){
                   // $page_list .= "<li><a onlick='javascript:void(0)' class='page active'>".$i."</a></li>";
                    $page_list .= "<li><a onlick='javascript:void(0)' class='page active'>".$i."</a></li>";
                }
                else{
                   // $page_list .= '<li><a href="'.$this->_link.'?page='.$i.'" title="Page '.$i.'" class="page">'.$i.'</a></li>';
                    $page_list .= '<li><a href="javascript:void(0)" onclick="Comparison.changePage('.$i.')" title="Page '.$i.'" class="page">'.$i.'</a></li>';
                }
                $page_list .= " ";
            }
            $page_list .= "<li>...</li>";
        }
        else {
            $page_list .= "<li>...</li>";
            for($i = $pages - 4;$i <= $pages; $i++){
                if($i == $curpage){
                   // $page_list .= "<li><a onlick='javascript:void(0)' class='page active'>".$i."</a></li>";
                    $page_list .= "<li><a onlick='javascript:void(0)' class='page active'>".$i."</a></li>";
                }
                else{
                    //$page_list .= '<li><a href="'.$this->_link.'?page='.$i.'" title="Page '.$i.'" class="page">'.$i.'</a></li>';
                    $page_list .= '<li><a href="javascript:void(0)" onclick="Comparison.changePage('.$i.')" title="Page '.$i.'" class="page">'.$i.'</a></li>';
                }
                $page_list .= " ";
            }
        }
        }
        if(($curpage+1)<=$pages){
          //  $page_list .= '<li class="button-page"><a href="'.$this->_link.'?page='.($curpage+1).'" title="Next" class="page">Next</a></li>';
            $page_list .= '<li class="button-page"><a href="javascript:void(0)" onclick="Comparison.changePage('.($curpage+1).')"  title="Next" class="page">Next</a></li>';
        }
        if(($curpage != $pages) && ($pages != 0)){
         //   $page_list .= '<li class="button-page"><a href="'.$this->_link.'?page='.$pages.'" title="Last" class="page"> Last</a></li>';
            $page_list .= '<li class="button-page"><a href="javascript:void(0)"onclick="Comparison.changePage('.$pages.')"  title="Last" class="page"> Last</a></li>';
        }
        $page_list .= '</ul>';
        return $page_list;
    }



    public function pagesListSearch($curpage){
        $total = $this->_total;
        $pages = $this->_pages;
        if($pages <=1){return '';}
        $page_list="<ul class='page-link'>";
        if($curpage!=1){
            // $page_list .= '<li class="button-page"><a href="'.$this->_link.'?page=1" title="First" class="page">First </a></li>';
            $page_list .= '<li class="button-page"><a href="javascript:void(0)" onclick="Comparison.changePageSearch(1)" title="First" class="page">First </a></li>';
        }
        if($curpage  > 1){
            //$page_list .= '<li class="button-page"><a href="'.$this->_link.'?page='.($curpage-1).'" title="Preview" class="page">Back</a></li>';
            $page_list .= '<li class="button-page"><a href="javascript:void(0)" onclick="Comparison.changePageSearch('.($curpage-1).')"  title="Preview" class="page">Back</a></li>';
        }
        if($curpage <= $this->_displaylink -2 ){
            for($i=1; ($i<= $this->_displaylink) and ($i<=$pages); $i++){
                if($i == $curpage){
                    //$page_list .= "<li><a onlick='javascript:void(0)' class='page active'>".$i."</a></li>";
                    $page_list .= "<li><a onlick='javascript:void(0)' class='page active'>".$i."</a></li>";
                }
                else{
                    //   $page_list .= '<li><a href="'.$this->_link.'?page='.$i.'" title="Page '.$i.'" class="page">'.$i.'</a></li>';
                    $page_list .= '<li><a href="javascript:void(0)" onclick="Comparison.changePageSearch('.$i.')" title="Page '.$i.'" class="page">'.$i.'</a></li>';
                }
                $page_list .= " ";
            }
            if($pages > $this->_displaylink) $page_list .= "<li>...</li>";
        }
        else {if($pages >= $curpage + 2){
            $page_list .= "<li>...</li>";
            for($i=$curpage-2; ($i <=$curpage+2) and ($i <= $pages); $i++){
                if($i == $curpage){
                    // $page_list .= "<li><a onlick='javascript:void(0)' class='page active'>".$i."</a></li>";
                    $page_list .= "<li><a onlick='javascript:void(0)' class='page active'>".$i."</a></li>";
                }
                else{
                    // $page_list .= '<li><a href="'.$this->_link.'?page='.$i.'" title="Page '.$i.'" class="page">'.$i.'</a></li>';
                    $page_list .= '<li><a href="javascript:void(0)" onclick="Comparison.changePageSearch('.$i.')" title="Page '.$i.'" class="page">'.$i.'</a></li>';
                }
                $page_list .= " ";
            }
            $page_list .= "<li>...</li>";
        }
        else {
            $page_list .= "<li>...</li>";
            for($i = $pages - 4;$i <= $pages; $i++){
                if($i == $curpage){
                    // $page_list .= "<li><a onlick='javascript:void(0)' class='page active'>".$i."</a></li>";
                    $page_list .= "<li><a onlick='javascript:void(0)' class='page active'>".$i."</a></li>";
                }
                else{
                    //$page_list .= '<li><a href="'.$this->_link.'?page='.$i.'" title="Page '.$i.'" class="page">'.$i.'</a></li>';
                    $page_list .= '<li><a href="javascript:void(0)" onclick="Comparison.changePageSearch('.$i.')" title="Page '.$i.'" class="page">'.$i.'</a></li>';
                }
                $page_list .= " ";
            }
        }
        }
        if(($curpage+1)<=$pages){
            //  $page_list .= '<li class="button-page"><a href="'.$this->_link.'?page='.($curpage+1).'" title="Next" class="page">Next</a></li>';
            $page_list .= '<li class="button-page"><a href="javascript:void(0)" onclick="Comparison.changePageSearch('.($curpage+1).')"  title="Next" class="page">Next</a></li>';
        }
        if(($curpage != $pages) && ($pages != 0)){
            //   $page_list .= '<li class="button-page"><a href="'.$this->_link.'?page='.$pages.'" title="Last" class="page"> Last</a></li>';
            $page_list .= '<li class="button-page"><a href="javascript:void(0)"onclick="Comparison.changePageSearch('.$pages.')"  title="Last" class="page"> Last</a></li>';
        }
        $page_list .= '</ul>';
        return $page_list;
    }
}