<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_FAQ
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves FAQ Extension
 *
 * @category   Ves
 * @package    Ves_FAQ
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_FAQ_SearchController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        if( !Mage::getStoreConfigFlag('ves_faq/general_setting/enable') ) {
            $this->norouteAction();
        }
    }
    /**
     * index action
     */
    public function indexAction()
    {
    	$keyword = $this->getRequest()->getParam('s');
    	if(strlen($keyword)<=0){
    		$this->_redirect('faq/index/index');
    	}
        $this->loadLayout();
        $this->renderLayout();
    }
}