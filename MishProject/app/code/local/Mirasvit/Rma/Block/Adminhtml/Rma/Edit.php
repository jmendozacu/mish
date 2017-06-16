<?php
class Mirasvit_Rma_Block_Adminhtml_Rma_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct ()
    {
        parent::__construct();
        $this->_objectId = 'rma_id';
        $this->_controller = 'adminhtml_rma';
        $this->_blockGroup = 'rma';


        $this->_updateButton('save', 'label', Mage::helper('rma')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('rma')->__('Delete'));


        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('rma')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action + 'back/edit/');
            }
        ";
        $rma = $this->getRma();
        if ($rma) {
            $order = $rma->getOrder();
            if ($order->canCreditmemo()) {
                $this->_addButton('order_creditmemo', array(
                    'label'     => Mage::helper('sales')->__('Credit Memo...'),
                    'onclick'   => 'var win = window.open(\'' . $this->getCreditmemoUrl($order) . '\', \'_blank\');win.focus();',
                ));
            }
        }

        return $this;
    }

    public function getCreditmemoUrl($order)
    {
        $collection = Mage::getModel('sales/order_invoice')->getCollection()
                    ->addFieldToFilter('order_id', $order->getId());
        // echo $collection->getSelect();die;
        if ($collection->count() == 1) {
            $invoice = $collection->getFirstItem();
            return $this->getUrl('adminhtml/sales_order_creditmemo/new', array('order_id' => $order->getId(), 'invoice_id' => $invoice->getId()));
        } else {
            return $this->getUrl('adminhtml/sales_order_creditmemo/new', array('order_id' => $order->getId()));
        }
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    public function getRma()
    {
        if (Mage::registry('current_rma') && Mage::registry('current_rma')->getId()) {
            return Mage::registry('current_rma');
        }
    }

    public function getHeaderText ()
    {
        if ($rma = $this->getRma()) {
            return Mage::helper('rma')->__("RMA #%s - %s", $rma->getIncrementId(), $rma->getStatus()->getName());
        } else {
            return Mage::helper('rma')->__('Create New RMA');
        }
    }

    /************************/

}