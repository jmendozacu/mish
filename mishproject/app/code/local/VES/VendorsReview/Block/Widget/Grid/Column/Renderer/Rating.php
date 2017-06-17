<?php

/**
 * Adminhtml grid item renderer
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsReview_Block_Widget_Grid_Column_Renderer_Rating
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
	/**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $review_id = $row->getReviewId();
        $total = 0;
        $votes = Mage::getModel('vendorsreview/vote')->getCollection()
        ->addFieldToFilter('review_id',$review_id);
        
        foreach($votes as $_vote) {
        	$total += $_vote->getRatePercents();
        }
        $rate_summary = ceil($total/$votes->count());
        
        $html = '<div class="rating-box">
                 	<div class="rating" style="width:' . $rate_summary . '%;"></div>
                 </div>';
        return $html;
    }
}
