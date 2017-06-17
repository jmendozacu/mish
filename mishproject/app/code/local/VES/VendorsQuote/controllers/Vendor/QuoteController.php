<?php
class VES_VendorsQuote_Vendor_QuoteController extends VES_Vendors_Controller_Action
{
	public function indexAction(){
    	$this->loadLayout()
    		->_setActiveMenu('sales')->_title($this->__('Quotations'))
    		->_addBreadcrumb(Mage::helper('vendorssales')->__('Sales'), Mage::helper('vendorssales')->__('Sales'))
        	->_addBreadcrumb(Mage::helper('vendorsquote')->__('Quotations'), Mage::helper('vendorsquote')->__('Quotations'));
		$this->renderLayout();
	}
	
	public function viewAction(){
	    
	    $quoteId = $this->getRequest()->getParam('quote_id');
	    try{
	    $quote = Mage::getModel('vendorsquote/quote')->load($quoteId);
	    if(!$quoteId || !$quote->getId() || 
	        $quote->getVendorId() != $this->_getSession()->getVendor()->getId()) 
	            throw new Mage_Core_Exception(Mage::helper('vendorsquote')->__('The quote is not available.'));
	    
	    Mage::register('quote', $quote);
	    Mage::register('current_quote', $quote);
	    $this->loadLayout()
    	    ->_setActiveMenu('sales')
    	    ->_title($this->__('Quotations'))->_title($this->__('#%s',$quote->getIncrementId()))
    	    ->_addBreadcrumb(Mage::helper('vendorssales')->__('Sales'), Mage::helper('vendorssales')->__('Sales'))
    	    ->_addBreadcrumb(Mage::helper('vendorsquote')->__('Quotations'), Mage::helper('vendorsquote')->__('Quotations'),Mage::getUrl('vendors/quote/index'))
	        ->_addBreadcrumb(Mage::helper('vendorsquote')->__('Quote #%s',$quote->getIncrementId()), Mage::helper('vendorsquote')->__('Quote #%s',$quote->getIncrementId()));
	    $this->renderLayout();
	    
	    }catch (Exception $e){
	        $this->_getSession()->addException($e, $e->getMessage());
	        $this->_redirect('*/*');
	    }
	}
	
	public function holdAction(){
	    $quoteId = $this->getRequest()->getParam('quote_id');
	    try{
	        $quote = Mage::getModel('vendorsquote/quote')->load($quoteId);
	        if(!$quoteId || !$quote->getId() ||
	            $quote->getVendorId() != $this->_getSession()->getVendor()->getId())
	                throw new Mage_Core_Exception(Mage::helper('vendorsquote')->__('The quote is not available.'));
	            
	        $quote->hold();
	        $this->_getSession()->addSuccess(Mage::helper('vendorsquote')->__('Quote is currently on hold.'));
	        $this->_redirect('*/*/view',array('quote_id'=>$quoteId));
	    }catch(Exception $e){
	        $this->_getSession()->addException($e, $e->getMessage());
	        $this->_redirect('*/*/index');
	    }
	}
	
	
	public function unholdAction(){
	    $quoteId = $this->getRequest()->getParam('quote_id');
	    try{
	        $quote = Mage::getModel('vendorsquote/quote')->load($quoteId);
	        if(!$quoteId || !$quote->getId() ||
	            $quote->getVendorId() != $this->_getSession()->getVendor()->getId())
	                throw new Mage_Core_Exception(Mage::helper('vendorsquote')->__('The quote is not available.'));
	             
	            $quote->unhold();
	            $this->_getSession()->addSuccess(Mage::helper('vendorsquote')->__('Quote is succesfully unhold!'));
	            $this->_redirect('*/*/view',array('quote_id'=>$quoteId));
	    }catch(Exception $e){
	        $this->_getSession()->addException($e, $e->getMessage());
	        $this->_redirect('*/*/index');
	    }
	}
	
	
	public function cancelAction(){
	    $quoteId = $this->getRequest()->getParam('quote_id');
	    try{
	        $quote = Mage::getModel('vendorsquote/quote')->load($quoteId);
	        if(!$quoteId || !$quote->getId() ||
	            $quote->getVendorId() != $this->_getSession()->getVendor()->getId())
	                throw new Mage_Core_Exception(Mage::helper('vendorsquote')->__('The quote is not available.'));
	    
	            $quote->cancel();
	            Mage::helper('vendorsquote')->sendCancelQuoteNotificationEmail($quote);
	            
	            $this->_getSession()->addSuccess(Mage::helper('vendorsquote')->__('Quote Request was successfully canceled'));
	            $this->_redirect('*/*/index');
	    }catch(Exception $e){
	        $this->_getSession()->addException($e, $e->getMessage());
	        $this->_redirect('*/*/index');
	    }
	}
	
	
	
	public function _getProposalQtyHtml($proposal,$i){
	    $qtyHtml = '';
	    $qtyHtml .= '<li class="proposal-container'.(($i ==0)?' first':'').'" id="proposal-qty-'.$proposal->getId().'">'.($proposal->getQty()*1).'</li>';
	    
	    return $qtyHtml;
	}
	/**
	 * Get proposal qty html
	 * @param VES_VendorsQuote_Model_Quote_Item $item
	 * @return string
	 */
	public function getProposalQtyHtml(VES_VendorsQuote_Model_Quote_Item $item){
	    $qtyHtml = '';
	    $i = 0;
	    foreach($item->getProposals() as $proposal){
	        $qtyHtml .= $this->_getProposalQtyHtml($proposal, $i);
	        $i ++;
	    }
	    
	    return $qtyHtml;
	}
	
	protected function _getProposalPriceHtml(VES_VendorsQuote_Model_Quote_Item $item,VES_VendorsQuote_Model_Quote_Item_Proposal $proposal,$i){
	    $priceHtml = '';
	    $priceHtml .= '<div class="clearer proposal-container'.(($i ==0)?' first':'').'" id="proposal-'.$proposal->getId().'">';
	    $priceHtml .= ' <input type="radio" class="proposal-radio" id="proposal-'.$proposal->getId().'-radio" value="'.$proposal->getId().'" data-item-id="'.$item->getId().'" name="item['.$item->getId().'][default_proposal]" '.(($item->getDefaultProposal()==$proposal->getId())?' checked="checked"':'').'>';
	    $priceHtml .= ' <input type="text" id="price-'.$proposal->getId().'" class="required-entry validate-zero-or-greater validate-number input-text proposalprice" size="3" value="'.$proposal->getPrice().'" name="proposal['.$item->getId().']['.$proposal->getId().'][price]" data-proposal="'.$proposal->getId().'" data-item-id="'.$item->getId().'"/>';
	    $priceHtml .= ' <a href="javascript: void(0);" class="remove-proposal" onclick="removeProposal('.$proposal->getId().',true)">'.$this->__('Remove').'</a>';
	    $priceHtml .= ' <a href="javascript: void(0);" class="save-proposal" style="display: none;" onclick="saveProposal('.$proposal->getId().','.$item->getId().')">'.$this->__('Save').'</a>';
	    $priceHtml .= '</div>';
	    return $priceHtml;
	}
	/**
	 * Get proposal price html
	 * @param VES_VendorsQuote_Model_Quote_Item $item
	 * @return string
	 */
	public function getProposalPriceHtml(VES_VendorsQuote_Model_Quote_Item $item){
	    $priceHtml = '';
	    $i = 0;
	    foreach($item->getProposals() as $proposal){
	        $priceHtml .= $this->_getProposalPriceHtml($item,$proposal,$i);
	        $i ++;
	    }
	     
	    return $priceHtml;
	}
	
	public function _getProposalMarginHtml(VES_VendorsQuote_Model_Quote_Item $item, VES_VendorsQuote_Model_Quote_Item_Proposal $proposal, $i){
	    $marginHtml = '';
	    $marginHtml .= '<li class="proposal-container'.(($i ==0)?' first':'').'" id="proposal-margin-'.$proposal->getId().'">'.round(($proposal->getPrice()-$item->getPrice())*100/$item->getPrice()).'%</li>';
	    return $marginHtml;
	}
	public function getProposalMarginHtml(VES_VendorsQuote_Model_Quote_Item $item){
	    $marginHtml = '';
	    $i = 0;
	    foreach($item->getProposals() as $proposal){
	        $marginHtml .= $this->_getProposalMarginHtml($item, $proposal, $i);
	    }
	
	    return $marginHtml;
	}
	
	public function saveProposalAction(){
	    $quoteItemId   = $this->getRequest()->getParam('quote_item_id','');
	    $proposalId    = $this->getRequest()->getParam('proposal_id',null);
	    $price         = $this->getRequest()->getParam('price',0);
	    $qty           = $this->getRequest()->getParam('qty',0);
	    $isFirst       = $this->getRequest()->getParam('is_first',0);
	    try{
	        $item = Mage::getModel('vendorsquote/quote_item')->load($quoteItemId);
	        if(!$item->getId()) throw new Mage_Core_Exception(Mage::helper('vendorsquote')->__('The quote item is not exist.'));
	        if($proposalId){
	            $proposal = $item->updateProposal($proposalId,$qty,$price);
	        }else{
	            $proposal = $item->addProposal($qty,$price);
	        }
	        $result = array(
	            'success'      => true,
	            'proposal_id'  => $proposal->getId(),
	            'qty_html'     => $this->_getProposalQtyHtml($proposal,1),
	            'price_html'   => $this->_getProposalPriceHtml($item,$proposal,$isFirst?0:1),
	            'margin_html'  => $this->_getProposalMarginHtml($item,$proposal,$isFirst?0:1),
	        );
	    }catch (Exception $e){
	        $result = array('success'=>false,'msg'=>$e->getMessage());
	    }
	    
	    $this->getResponse()->setBody(json_encode($result));
	}
	
	public function saveAllProposalAction(){
	    $quoteItemId   = $this->getRequest()->getParam('quote_item_id','');
	    $data      = $this->getRequest()->getParam('data');
	    $data      = json_decode($data,true);
	    $result    = array();
	    
	    try{
	        if(!is_array($data))  throw new Mage_Core_Exception(Mage::helper('vendorsquote')->__('The submited data is not valid.'));
	        
	        $item = Mage::getModel('vendorsquote/quote_item')->load($quoteItemId);
	        if(!$item->getId()) throw new Mage_Core_Exception(Mage::helper('vendorsquote')->__('The quote item is not exist.'));
	        foreach($data as $proposalId=>$proposalData){
    	        if($proposalData['new_object']){
    	            $proposal = $item->addProposal($proposalData['qty'],$proposalData['price']);
    	        }else{
    	            $proposal = $item->updateProposal($proposalId,$proposalData['qty'],$proposalData['price']);
    	        }
	        }
	        $result = array(
	            'success'      => true,
	            'item_id'      => $proposal->getId(),
	            'qty_html'     => $this->getProposalQtyHtml($item),
	            'price_html'   => $this->getProposalPriceHtml($item),
	            'margin_html'  => $this->getProposalMarginHtml($item),
	        );
	    }catch (Exception $e){
	        $result = array('success'=>false,'msg'=>$e->getMessage());
	    }
	    
	    $this->getResponse()->setBody(json_encode($result));
	}
	
	
	public function saveDefaultProposalAction(){
	    $quoteItemId   = $this->getRequest()->getParam('quote_item_id','');
	    $proposalId    = $this->getRequest()->getParam('proposal_id',null);
	    try{
	        $item = Mage::getModel('vendorsquote/quote_item')->load($quoteItemId);
	        $item->setData('default_proposal',$proposalId)->save();
	        $result = array('success'=>true);
	    }catch (Exception $e){
	        $result = array('success'=>false,'msg'=>$e->getMessage());
	    }
	     
	    $this->getResponse()->setBody(json_encode($result));
	}
	
	public function removeProposalAction(){
	    $proposalId    = $this->getRequest()->getParam('proposal_id',null);
	    try{
	        $proposal = Mage::getModel('vendorsquote/quote_item_proposal')->setId($proposalId)->delete();
	        $result = array('success'=>true);
	    }catch (Exception $e){
	        $result = array('success'=>false,'msg'=>$e->getMessage());
	    }
	    
	    $this->getResponse()->setBody(json_encode($result));
	}
	
	
	public function loadProductGridAction(){
	    $this->getResponse()->setBody($this->getLayout()->createBlock('vendorsquote/vendor_quote_view_items_add_grid')->toHtml());
	}
	
	public function configureProductToAddAction(){
	    $productId  = (int) $this->getRequest()->getParam('id');
	    
	    $configureResult = new Varien_Object();
	    $configureResult->setOk(true);
	    $configureResult->setProductId($productId);
	    $sessionQuote = Mage::getSingleton('adminhtml/session_quote');
	    $configureResult->setCurrentStoreId($sessionQuote->getStore()->getId());
	    $configureResult->setCurrentCustomerId($sessionQuote->getCustomerId());
	    
	    // Render page
	    /* @var $helper Mage_Adminhtml_Helper_Catalog_Product_Composite */
	    $helper = Mage::helper('adminhtml/catalog_product_composite');
	    $helper->renderConfigureResult($this, $configureResult);
	    
	    return $this;
	}
	/**
	 * Process buyRequest file options of items
	 *
	 * @param array $items
	 * @return array
	 */
	protected function _processFiles($items)
	{
	    /* @var $productHelper Mage_Catalog_Helper_Product */
	    $productHelper = Mage::helper('catalog/product');
	    foreach ($items as $id => $item) {
	        $buyRequest = new Varien_Object($item);
	        $params = array('files_prefix' => 'item_' . $id . '_');
	        $buyRequest = $productHelper->addParamsToBuyRequest($buyRequest, $params);
	        if ($buyRequest->hasData()) {
	            $items[$id] = $buyRequest->toArray();
	        }
	    }
	    return $items;
	}
	
	public function addProductAction(){
	    $quoteId = $this->getRequest()->getParam('quote');
	    $quote = Mage::getModel('vendorsquote/quote')->load($quoteId);
	    
	    if ($this->getRequest()->has('item')){
    	    $items = $this->getRequest()->getPost('item');
            $items = $this->_processFiles($items);
            foreach($items as $productId=>$item){
                $product = Mage::getModel('catalog/product')->load($productId);
                if(!$product->getId()) continue;
                unset($item['_processing_params']);
                $item['id']         = $product->getId();
                $item['product']    = $product->getId();
                $quote->addProduct($product,new Varien_Object($item));
            }
	    }
	    $quote->collectTotals();
	    
	    $this->_redirect('*/*/loadBlocks',array('quote'=>$quote->getId()));
	}
	
	public function removeQuoteItemAction(){
	    $quoteItemId = $this->getRequest()->getParam('quote_item_id');
	    try{
    	    $item = Mage::getModel('vendorsquote/quote_item')->load($quoteItemId);
    	    if(!$quoteItemId || !$item->getId()) throw new Mage_Core_Exception(Mage::helper('vendorsquote')->__('Quote item is not exist.'));
    	    $quoteId = $item->getQuoteId();
    	    $item->delete();
    	    
    	    $quote = Mage::getModel('vendorsquote/quote')->load($quoteId);
    	    Mage::register('quote', $quote);
    	    Mage::register('current_quote', $quote);
    	    
    	    $update = $this->getLayout()->getUpdate();
    	    $update->addHandle('vendors_quote_items');
    	    $this->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
    	    $itemBlock         = $this->getLayout()->getBlock('quote_items');
    	    $result = array('success'=>true,'items'=>$itemBlock->toHtml());
    	    
	    }catch (Exception $e){
	        $result = array('success'=>false,'msg'=>$e->getMessage());
	    }
	    
	    $this->getResponse()->setBody(json_encode($result));
	}
	
	public function loadBlocksAction(){
	    $quoteId = $this->getRequest()->getParam('quote');
	    $quote = Mage::getModel('vendorsquote/quote')->load($quoteId);
	    
	    Mage::register('quote', $quote);
	    Mage::register('current_quote', $quote);
	     
	    $update = $this->getLayout()->getUpdate();
	    $update->addHandle('vendors_quote_items');
	    $this->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
	    $itemBlock         = $this->getLayout()->getBlock('quote_items');
	    //$searchBlock       = $this->getLayout()->getBlock('search');
	    $iFrameresponse    = array('items'=>$itemBlock->toHtml()/*,'search'=>$searchBlock->toHtml()*/);
	     
	    $itemsHtml = '<script type="text/javascript">//<![CDATA['."\n";
	    $itemsHtml.= 'var iFrameResponse = '.json_encode($iFrameresponse).';';
	    $itemsHtml.= "\n//]]></script>";
	    $this->getResponse()->setBody($itemsHtml);
	}
	
	public function newAction(){
	    $this->loadLayout()
	        ->_setActiveMenu('sales')
    	    ->_title($this->__('Quotations'))->_title($this->__('New'))
    	    ->_addBreadcrumb(Mage::helper('vendorssales')->__('Sales'), Mage::helper('vendorssales')->__('Sales'))
    	    ->_addBreadcrumb(Mage::helper('vendorsquote')->__('Quotations'), Mage::helper('vendorsquote')->__('Quotations'),Mage::getUrl('vendors/quote/index'))
	        ->_addBreadcrumb(Mage::helper('vendorsquote')->__('New'));
	    $this->renderLayout();
	}
	
	
	public function prepareQuoteAction(){
	    $quote = Mage::getModel('vendorsquote/quote');
	    $quote->addData($this->getRequest()->getPost());
	    $quote->setVendorId($this->_getSession()->getVendor()->getId())
	       ->setStatus(VES_VendorsQuote_Model_Quote::STATUS_CREATED_NOT_SENT)
	       ->save();
	    $this->_redirect('*/*/view',array('quote_id'=>$quote->getId()));
	}
	
	
	public function saveAction(){
	    $quoteData = $this->getRequest()->getParam('quote',array());
	    $proposalData = $this->getRequest()->getParam('proposal',array());
	    $itemData  = $this->getRequest()->getParam('item',array());
	    
	    try{
	        $quoteId = isset($quoteData['id'])?$quoteData['id']:'';
    	    $quote = Mage::getModel('vendorsquote/quote')->load($quoteId);
    	    if(!$quoteId || !$quote->getId()){
    	        throw new Mage_Core_Exception(Mage::helper('vendorsquote')->__('The quote is not exist.'));
    	    }
    	    
    	    
    	    /*Save item data*/
    	    $itemArray = array();
    	    foreach($itemData as $itemId=>$item){
    	        $quoteItemObject = $quote->getItemById($itemId);
    	        $itemArray[$itemId] = $quoteItemObject; /*Store item objects to an array to re-use it in processing proposal data*/
    	        if(isset($item['default_proposal'])){
    	            $quoteItemObject->setData('default_proposal',$item['default_proposal'])->save();
    	        }
    	    }
    	    
    	    /*Save proposal data*/
    	    foreach($proposalData as $itemId=>$itemProposals){
    	        $quoteItemObject = isset($itemArray[$itemId])?$itemArray[$itemId]:Mage::getModel('vendorsquote/quote')->getItemById($itemId);
    	        foreach($itemProposals as $proposalId=>$proposalData){
    	            $proposalObject = Mage::getModel('vendorsquote/quote_item_proposal')->load($proposalId);
    	            $proposalObject->addData($proposalData)->setItem($quoteItemObject)->save();
    	        }
    	    }
    	    
    	    /* Save quote data */
    	    $quote->addData($quoteData);
    	    $isSubmitProposal = $this->getRequest()->getParam('submit_proposal');
    	    if($isSubmitProposal){
    	        $quote->setStatus(VES_VendorsQuote_Model_Quote::STATUS_SENT);
    	        $this->_getSession()->addSuccess(Mage::helper('vendorsquote')->__('Quote Request was successfully saved.'));
    	        Mage::helper('vendorsquote')->sendNewProposalNotificationEmailToCustomer($quote);
    	        
    	        $this->_getSession()->addSuccess(Mage::helper('vendorsquote')->__('Email was sent to customer.'));
    	        $quote->submit();
    	    }else{
    	        $quote->save();
    	        $this->_getSession()->addSuccess(Mage::helper('vendorsquote')->__('Quote Request was successfully saved.'));
    	    }
    	    $this->_redirect('*/*/view',array('quote_id'=>$quoteId));
	    }catch (Exception $e){
	        $this->_getSession()->addException($e, $e->getMessage());
	        $this->_redirect('*/*/');
	    }
	}
	
	
	public function sendQuoteMessageAction(){
	    $quoteId = $this->getRequest()->getParam('quote_id','');
	    $message = $this->getRequest()->getParam('message','');
	    $isNotifyCustomer = $this->getRequest()->getParam('is_notify_customer',0);
	    try{
	       $quote = Mage::getModel('vendorsquote/quote')->load($quoteId);
	       if(!$quoteId || !$quote->getId() || $quote->getVendorId() != $this->_getSession()->getVendor()->getId()) 
	           throw new Mage_Core_Exception(Mage::helper('vendorsquote')->__('The quote is not exist'));
	       
	       Mage::register('quote', $quote);
	       Mage::register('current_quote', $quote);
	       
	       $message = $quote->addMessage($this->_getSession()->getVendor()->getTitle(), $message,VES_VendorsQuote_Model_Quote_Message::TYPE_VENDOR, $isNotifyCustomer);
	       if($isNotifyCustomer) Mage::helper('vendorsquote')->sendQuoteNotificationMessage($message, $quote);
	       
	       $messageListBlock = $this->getLayout()->createBlock('vendorsquote/vendor_quote_view_tab_info_message','message_list')
               ->setTemplate('ves_vendorsquote/quote/view/tab/info/message.phtml');
	       $result = array('success'=>true,'message_list'=>$messageListBlock->toHtml());
	    }catch (Exception $e){
	        $result = array('success'=>false,'msg'=>$e->getMessage());
	    }
	    
	    $this->getResponse()->setBody(json_encode($result));
	}
	
	public function massStatusAction(){
	    $quoteIds = $this->getRequest()->getParam('quotes');
	    $quoteIds = explode(",", $quoteIds);
	    $status   = $this->getRequest()->getParam('status');
	    try{
	        foreach($quoteIds as $quoteId){
	            $quote = Mage::getModel('vendorsquote/quote')->load($quoteId);
	            $quote->setStatus($status)->save();
	        }
	        $this->_getSession()->addSuccess(
	            $this->__('Total of %d record(s) were successfully updated', count($quoteIds))
	        );
	    }catch (Exception $e){
	        $this->_getSession()->addError($e->getMessage());
	    }
	    $this->_redirect('*/*/');
	}
}