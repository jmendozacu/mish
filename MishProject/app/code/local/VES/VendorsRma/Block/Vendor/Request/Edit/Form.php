<?php

class VES_VendorsRma_Block_Vendor_Request_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

  public function __construct()
    {
        parent::__construct();

        $request = Mage::registry("current_request");
        if($request->getId()){
            $this->setTemplate('ves_vendorsrma/request/edit/form.phtml');
        }


  }

  protected function _prepareForm()
  {
      $form = new Varien_Data_Form(array(
                                      'id' => 'edit_form',
                                      'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      );

      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }

    protected function _prepareLayout()
    {
        $this->setChild('tabs',
            $this->getLayout()->createBlock('vendorsrma/vendor_request_edit_tabs', 'tabs')
         );
         return parent::_prepareLayout();
    }
    public function getTabsHtml()
    {
          return $this->getChildHtml('tabs');
    }

}