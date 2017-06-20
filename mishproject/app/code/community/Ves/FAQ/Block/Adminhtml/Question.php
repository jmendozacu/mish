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
class Ves_FAQ_Block_Adminhtml_Question extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_question';
		$this->_blockGroup = 'ves_faq';
		$this->_headerText = Mage::helper('ves_faq')->__('Question Manager');
		$this->_addButtonLabel = Mage::helper('ves_faq')->__('Add Question');
		parent::__construct();
	}

	protected function _prepareLayout(){
		$this->_addButton('add_new', array(
			'label'   => Mage::helper('catalog')->__('Import CSV'),
			'onclick' => "setLocation('{$this->getUrl('*/*/uploadCsv')}')",
			'class'   => 'add'
			));
		$this->setChild('grid', $this->getLayout()->createBlock('adminhtml/catalog_product_grid', 'product.grid'));
		return parent::_prepareLayout();
	}
}