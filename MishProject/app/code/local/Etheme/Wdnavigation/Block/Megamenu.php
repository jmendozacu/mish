<?php
class Etheme_Wdnavigation_Block_Megamenu extends Mage_Catalog_Block_Navigation
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

    protected function _renderCategoryMenuItemHtml($category, $level = 0, $isLast = false, $isFirst = false,
                                                   $isOutermost = false, $outermostItemClass = '', $childrenWrapClass = '', $noEventAttributes = false)
    {
        if (!$category->getIsActive()) {
            return '';
        }
        $html = array();
        $black_list=Mage::helper('welldone')->getConfigOption('megamenu/black_list');
        $black_list=explode(',',$black_list);
        if(in_array($category->getId(),$black_list))return;

        // get all children
        // If Flat Data enabled then use it but only on frontend
        $flatHelper = Mage::helper('catalog/category_flat');
        if ($flatHelper->isAvailable() && $flatHelper->isBuilt(true) && !Mage::app()->getStore()->isAdmin()) {
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

        if($level==0)
        {
            $category_data=Mage::getModel('catalog/category')->load($category->getId());
            $label= $category_data->getWd_category_lable();
            $classes[]='level-menu';
            //$classes[]='compact-hidden';
        }

        // prepare list item attributes
        $attributes = array();
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }

        // assemble list item with attributes
        $htmlLi = '<li';
        foreach ($attributes as $attrName => $attrValue) {
            $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
        }
        $htmlLi .= '>';
        $html[] = $htmlLi;

        if($level==0) {
            $html[] = '<ul>';
            $html[] = '<li class="title">';
        }

        $html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
        $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
        $html[] = '</a>';

        if($level==0) {
            if ($label) {
                $color='';
                $label=explode('||',$label);
                if(isset($label[1])){
                    $color='style="background-color:'.$label[1].'"';
                }
                if(!empty($label))$html[]='<span class="badge badge--squared" '.$color.'>'.$label[0].'</span>';
            }
            $html[]='</li>';
        }

        // render children{}
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
            /*if ($childrenWrapClass) {
                $html[] = '<div class="' . $childrenWrapClass . '">';
            }*/
            if ($level==0) {
                $html[] = $htmlChildren;
            } else {
                $html[] = '<ul class="level-menu__dropdown">';
                $html[] = $htmlChildren;
                $html[] = '</ul>';
            }


            /*if ($childrenWrapClass) {
                $html[] = '</div>';
            }*/
        }

        if($level==0)
        {
            $html[]='</ul>';
        }

        $html[] = '</li>';

        $html = implode("\n", $html);
        return $html;
    }
}