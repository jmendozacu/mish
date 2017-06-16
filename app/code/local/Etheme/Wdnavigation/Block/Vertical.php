<?php
class Etheme_Wdnavigation_Block_Vertical extends Mage_Catalog_Block_Navigation
{

    /**
     * Render category to html
     *
     * @param Mage_Catalog_Model_Category $category
     * @param int Nesting level number
     * @param boolean Whether ot not this item is last, affects list item class
     * @param boolean Whether ot not this item is first, affects list item class
     * @param boolean Whether ot not this item is outermost, affects list item class
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @param boolean Whether ot not to add on* attributes to list item
     * @return string
     */

    protected function _renderCategoryMenuItemHtml($category, $level = 0, $isLast = false, $isFirst = false, $isOutermost = false, $outermostItemClass = '', $childrenWrapClass = '', $noEventAttributes = false,$nav_simple_render=false)
    {
        if (!$category->getIsActive()) {
            return '';
        }
        $html = array();
        $black_list=Mage::helper('welldone')->getConfigOption('megamenu/black_list_left');
        $black_list=explode(',',$black_list);
        if(in_array($category->getId(),$black_list))return;
        // get all children
        // If Flat Data enabled then use it but only on frontend
        $flatHelper = Mage::helper('catalog/category_flat');
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $children = (array)$category->getChildrenNodes();
            $childrenCount = count($children);
        } else {
            $children = $category->getChildren();
            $childrenCount = $children->count();
        }
        $hasChildren = ($children && $childrenCount);

        // select active children
        $activeChildren = array();
        foreach ($children as $child) {
            if ($child->getIsActive()) {
                $activeChildren[] = $child;
            }
        }
        $activeChildrenCount = count($activeChildren);
        $hasActiveChildren = ($activeChildrenCount > 0);

        // prepare list item html classes
        $classes = array();


        $nav_simple = Mage::getModel('catalog/category')->load($category->getId())->getWd_nav_simple();

        //MEGAMENU VERTICAL NOT SIMPLE
        if(!$nav_simple && !$nav_simple_render)
        {
            if($level==0)
            {
                $classes[]='item';
                //$classes[]='compact-hidden';
            }
            if($level==0 && $hasChildren){
                $classes[]='menu-large';
            }
            if($level==1){
                $classes[]='level-menu';
            }

            $classes[] = 'level' . $level;
            $classes[] = 'nav-' . $this->_getItemPosition($level);
            if ($this->isCategoryActive($category)) {
                if($level!=0) $classes[] = 'active'; else $classes[] = 'current';
            }
            $linkClass = '';
            if ($isOutermost && $outermostItemClass) {
                $classes[] = $outermostItemClass;
                $linkClass = ' class="'.$outermostItemClass.'"';
            }
            if ($isFirst) {
                $classes[] = 'first';
            }
            if ($isLast) {
                $classes[] = 'last';
            }
            if ($hasActiveChildren) {
                $classes[] = 'parent';
            }

            if($level==0 or $level==1) {
                $category_data=Mage::getModel('catalog/category')->load($category->getId());
            }

            if($level==0)
            {
                //$advanced=$category_data->getBs_menuadvanced();
                $html_top=$category_data->getWd_nav_top();
                $html_top = Mage::helper('cms')->getBlockTemplateProcessor()->filter($this->helper('catalog/output')->categoryAttribute($category, $html_top, 'wd_nav_top'));
                $html_btm = $category_data->getWd_nav_btm();
                $html_btm = Mage::helper('cms')->getBlockTemplateProcessor()->filter($this->helper('catalog/output')->categoryAttribute($category, $html_btm, 'wd_nav_btm'));
                $html_right = Mage::getModel('catalog/category')->load($category->getId())->getWd_nav_right();
                $html_right = Mage::helper('cms')->getBlockTemplateProcessor()->filter($this->helper('catalog/output')->categoryAttribute($category, $html_right, 'wd_nav_right'));
            }

            // prepare list item attributes
            $attributes = array();
            if (count($classes) > 0) {
                $attributes['class'] = implode(' ', $classes);
            }



            $htmlLi = '<li';
            foreach ($attributes as $attrName => $attrValue) {
                $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
            }
            $htmlLi .= '>';

            if($level==1)$htmlLi.='<ul>';

            $html[] = $htmlLi;
            if($level==1)$html[] = '<li class="title">';
            if($level==0)$html[] = '<a href="'.$this->getCategoryUrl($category).'" class="dropdown-toggle">';
            else $html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
            $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
            if($level==0 && $hasActiveChildren)$html[]='<span class="caret caret--dots"></span>';
            if($level==0 && $badge=$category_data->getWd_category_lable())
            {
                $badge=explode('||',$badge);
                $html[] = '<span class="badge">'.$badge[0].'</span>';
            }

            $html[] = '</a>';
            if($level==1)$html[] = '</li>';
            if($level==0 && $hasChildren)$html[]='<div class="dropdown-menu megamenu animated fadeIn"><div class="container">';



            // render children
            $htmlChildren = '';
            $j = 0;
            foreach ($activeChildren as $child) {
                $htmlChildren .= $this->_renderCategoryMenuItemHtml(
                    $child,
                    ($level + 1),
                    ($j == $activeChildrenCount - 1),
                    ($j == 0),
                    false,
                    $outermostItemClass,
                    $childrenWrapClass,
                    $noEventAttributes
                );
                $j++;
            }
            if (!empty($htmlChildren)) {

                if($level==0){
                   if(!empty($html_right)) $html[] = '<ul class="level' . $level . ' megamenu__columns ">';
                    else $html[] = '<ul class="level' . $level . ' megamenu__columns megamenu__columns-image-off">';
                }
                elseif($level!=1) {
                    $html[] = '<ul class="level' . $level . '">';
                }

                if($level==0 && $hasChildren && !empty($html_top))
                {
                    $html[]='<li class="megamenu__columns__top-block text-uppercase">'.$html_top.'</li>';
                }
                $html[] = $htmlChildren;
                if($level==0 && $hasChildren && !empty($html_btm))
                {
                    $html[]='<li class="megamenu__columns__bottom-block text-uppercase">'.$html_btm.'</li>';
                }
                if($level==0 && $hasChildren && !empty($html_right))
                {
                    $html[]='<li class="megamenu__columns__side-image">'.$html_right.'</li>';
                }
                if($level!=1)$html[] = '</ul>';
            }


            if($level==0 && $hasChildren)$html[]='</div></div>';

            if($level==1)$html[]='</ul>';
            $html[] = '</li>';
        } elseif(($nav_simple  && $level==0) || $nav_simple_render)
        {
        //MEGAMENU SIMPLE
            if($level==0 && $hasChildren){
                $classes[]='dropdown-toggle';
            }
            $classes[] = 'level' . $level;
            $classes[] = 'nav-' . $this->_getItemPosition($level);
            if ($this->isCategoryActive($category)) {
                $classes[] = 'active';
            }
            $linkClass = '';
            if ($isOutermost && $outermostItemClass) {
                $classes[] = $outermostItemClass;
                $linkClass = ' class="'.$outermostItemClass.'"';
            }
            if ($isFirst) {
                $classes[] = 'first';
            }
            if ($isLast) {
                $classes[] = 'last';
            }
            if ($hasActiveChildren) {
                $classes[] = 'parent';
            }

            // prepare list item attributes
            $attributes = array();
            if (count($classes) > 0) {
                $attributes['class'] = implode(' ', $classes);
            }
            if ($hasActiveChildren && !$noEventAttributes) {
                $attributes['onmouseover'] = 'toggleMenu(this,1)';
                $attributes['onmouseout'] = 'toggleMenu(this,0)';
            }

            // assemble list item with attributes
            $htmlLi = '<li';
            foreach ($attributes as $attrName => $attrValue) {
                $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
            }
            $htmlLi .= '>';
            $html[] = $htmlLi;


            $html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
            $html[] = '<span class="link-name">' . $this->escapeHtml($category->getName()) . '</span>';
            if($level==0 && $hasActiveChildren)$html[]='<span class="caret caret--dots"></span>';
            $html[] = '</a>';
            // render children
            $htmlChildren = '';
            $j = 0;
            foreach ($activeChildren as $child) {
                $htmlChildren .= $this->_renderCategoryMenuItemHtml(
                    $child,
                    ($level + 1),
                    ($j == $activeChildrenCount - 1),
                    ($j == 0),
                    false,
                    $outermostItemClass,
                    $childrenWrapClass,
                    $noEventAttributes,
                    true
                );
                $j++;
            }
            if (!empty($htmlChildren)) {
                if ($childrenWrapClass) {
                    $html[] = '<div class="' . $childrenWrapClass . '">';
                }
               $html[] = '<ul class="dropdown-menu animated level-menu__dropdown  level' . $level . '" role="menu">';

                $html[] = $htmlChildren;
                $html[] = '</ul>';
                if ($childrenWrapClass) {
                    $html[] = '</div>';
                }
            }
            $html[] = '</li>';
        }
        $html = implode("\n", $html);
        return $html;
    }
}