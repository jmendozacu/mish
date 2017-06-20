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
 * @package    Ves_Blog
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves Blog Extension
 *
 * @category   Ves
 * @package    Ves_Blog
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_Blog_Block_Blog_Post_Comment extends Ves_Blog_Block_Blog_Template
{

	public function getCollection(){
		$page = $this->getRequest()->getParam('page') ? $this->getRequest()->getParam('page') : 1;
		$comment = Mage::getModel('ves_blog/comment')->getCollection()
		->addEnableFilter( 1  )
		->setPageSize( $this->getGeneralConfig("comment_limit") )
		->setCurPage($page)
		->addPostFilter( $this->getRequest()->getParam('id')  )
		->addStoreFilter(Mage::app()->getStore()->getId());

		return $comment;
	}

	public function getCountingComment(){
		$comment = Mage::getModel('ves_blog/comment')->getCollection()
		->addEnableFilter( 1  )
		->addStoreFilter(Mage::app()->getStore()->getId())
		->addPostFilter( $this->getRequest()->getParam('id')  );
		Mage::register( 'paginateTotal', count($comment) );
		Mage::register( "paginateLimitPerPage", $this->getGeneralConfig("comment_limit") );
		return count($comment);
	}

	public function getReCaptcha(){
		return Mage::helper('ves_blog/recaptcha')
		->setKeys( $this->getGeneralConfig("privatekey"), $this->getGeneralConfig("publickey") )
		->getReCapcha();
	}
}