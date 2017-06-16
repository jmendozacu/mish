<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * MageWorx DeliveryZone extension
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_Deliveryzone_Block_Adminhtml_Manager_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('catalog_category_products');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);

        $this->setDefaultFilter(array('productids'=>1));
//        $this->setSaveParametersInSession(true);
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'productids') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
    
     protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addStoreFilter($this->getRequest()->getParam('store'));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('productids', array(
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'productids',
                'values'    => $this->_getSelectedProducts(),
                'align'     => 'center',
                'index'     => 'entity_id',
                
            ));
        
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '80',
            'index'     => 'sku'
        ));
        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));
        $this->addColumn('price', array(
            'header'    => Mage::helper('catalog')->__('Price'),
            'type'  => 'currency',
            'width'     => '1',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'     => 'price'
        ));
       
        return parent::_prepareColumns();
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/ajaxGrid', array('_current'=>true,'ajax'=>true));
    }

    protected function _getSelectedProducts()
    {
        $products = array();
        $products = Mage::registry('selected_products');
        //$products = $this->getRequest()->getParam('selected_products');
        return $products;
    }
        
    public function _toHtml() {
        $html = parent::_toHtml();
        $html = str_replace('</table>', "</table>
            <script type='text/javascript'>
            $$('#catalog_category_products_table input[type=checkbox]').each(function(id) {
                $(id).observe('change', function(event){
                    var values = $('product_ids').value;
                    var list_values = values.split(',');
                    if(this.checked == false) {
                        if(this.value =='on') {
                            removeAllVisibleProducts();
                        } else {
                            for(var i = 0, l = list_values.length; i < l; i++)  {
                                if(list_values[i] == this.value) {
                                    list_values.splice(i,1);
                                }
                            }
                            $('product_ids').value = list_values.join(',');
                        }
                    } else {
                        if(this.value =='on') {
                            addAllVisibleProducts();
                        } else {
                            list_values.push(this.value);
                            $('product_ids').value = list_values.join(',');
                        }
                    }
                });
            });
            

            function removeAllVisibleProducts() {
                values = $('product_ids').value;
                list_values = values.split(',');
                for(var i = 0, l = list_values.length; i < l; i++)  {
                    $$('#catalog_category_products_table tbody input[type=checkbox]').each(function(id) {
                        if(list_values[i] == $(id).value) {
                            list_values.splice(i,1);
                        }
                    });
                    
                }
                $('product_ids').value = list_values.join(',');
            }
            
            function addAllVisibleProducts() {
                values = $('product_ids').value;
                list_values = values.split(',');
                $$('#catalog_category_products_table tbody input[type=checkbox]').each(function(id) {
                    list_values.push($(id).value);
                });
                $('product_ids').value = list_values.join(',');
            }
            </script>
        ", $html);
        return $html;
    }
}