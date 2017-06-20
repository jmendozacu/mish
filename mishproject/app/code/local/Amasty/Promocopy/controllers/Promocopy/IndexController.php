<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
* @package Amasty_Promocopy
*/
class Amasty_Promocopy_Promocopy_IndexController extends VES_Vendors_Controller_Action
{
    protected $gridUrl = 'vendors/promo_quote/index';
   
    public function indexAction()
    {
        //return $this->_fault($this->__('Please select some action.'));   
        $this->loadLayout();
        $this->renderLayout();     
    }
    // protected function _isAllowed()
    // {
    //     return Mage::helper('ampromocopy')->moduleEnable();
    // }
    
    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('ampromocopy')
            ->_title($this->__('Module'))
            ->_title($this->__('ampromocopy'))
            ->_addBreadcrumb($this->__('ampromocopy'), $this->__('Module'));
            /*->_addBreadcrumb($this->__('commission'), $this->__('commission'))*/
        
        return $this;
    }   
   
    public function duplicateAction()
    {
        $id = $this->getRequest()->getParam('rule_id');
        if (!$id) {
            return $this->_fault($this->__('Please select a rule to duplicate.'));
        }
        
        try {
            $mainRule = Mage::getSingleton('salesrule/rule')->load($id);
            if (!$mainRule->getId()){
                return $this->_fault($this->__('Please select a rule to duplicate.'));
            }
            
            //just pre-load values
            $mainRule->getStoreLabels();
            
            // a proper clone function has not been implemented 
            // for the rule class, so we need to unlink coupon object manually
            $rule = clone $mainRule;
            $oldCoupon = $rule->acquireCoupon();
            if ($oldCoupon){
                $oldCoupon->setId(0);
            }
            
            // set default data
            $rule->setCouponType(Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON);
            $rule->setIsActive(0);
            
            //create new acually
            $rule->setId(null);
            $rule->save();
            
            $this->_getSession()->addSuccess(
                $this->__('The rule has been duplicated. Set a new coupon and activate it if needed.')
            );
            return $this->_redirect('vendors/promo_quote/index/', array('id' => $rule->getId()));            
        } 
        catch (Exception $e) {
            return $this->_fault($e->getMessage());
        } 
        
        //unreachable 
        return $this;  
    }       
    
    public function moveUpAction()
    {
         $this->_move('up');

         $redirect_URl=explode(Mage::getBaseUrl(),Mage::helper('core/http')->getHttpReferer()); 

         if (strpos($redirect_URl[1], 'admin') !== false) 
           {
            $newredirectURL=explode("admin/",$redirect_URl[1]);
        
            return $this->_redirect('adminhtml/'.$newredirectURL[1]);
             
           }

           if (strpos($redirect_URl[1], 'admin') == false) 
           {
            $this->_redirect( $redirect_URl[1]);
             return true;
           }
    }
    
    public function moveDownAction()
    {
         $this->_move('doen');

         $redirect_URl=explode(Mage::getBaseUrl(),Mage::helper('core/http')->getHttpReferer()); 

         if (strpos($redirect_URl[1], 'admin') !== false) 
           {
            $newredirectURL=explode("admin/",$redirect_URl[1]);
        
            return $this->_redirect('adminhtml/'.$newredirectURL[1]);
             
           }

           if (strpos($redirect_URl[1], 'admin') == false) 
           {
            $this->_redirect( $redirect_URl[1]);
             return true;
           }
    }
    
    protected function _move($direction)
    {
        $idss = $this->getRequest()->getParam('rules');
         $ids = explode(',', $idss);
        if (!is_array($ids)) {
            return $this->_fault($this->__('Please select rule(s).'));
        }
        $num = 0;
        
        $db     = Mage::getSingleton('core/resource')->getConnection('core_write');  
        $table  = Mage::getSingleton('core/resource')->getTableName('salesrule/rule');        
        foreach ($ids as $id) {
            try {
                $select = $db->select()->from($table)->where('rule_id = ?', $id)->limit(1);
                $row = $db->fetchRow($select);
                if (!$row){
                    $this->_fault($this->__('Can not find rule with id=%s.', $id));
                    continue;
                }
                   
                if ('up' == $direction){ // move up
                    $select = $db->select()
                        ->from($table, array(new Zend_Db_Expr('MIN(sort_order)')))
                        ->where('sort_order <= ? ', $row['sort_order'])
                        ->where('rule_id != ?', $row['rule_id']);
                    $minPos = $db->fetchOne($select); 
                    
                    if (is_null($minPos)) // it is already the first item
                        continue;
                    
                    if ($minPos == 0){
                        $db->update($table, array('sort_order' => new Zend_Db_Expr('sort_order+1')));
                        $minPos=1;
                    }
                    if ($row['sort_order'] >= $minPos){
                        $db->update($table, array('sort_order'=>$minPos-1), 
                            'rule_id =' . intval($row['rule_id']));  
                         ++$num;   
                    }                    
                } 
                else { // move down
                    $select = $db->select()
                        ->from($table, array(new Zend_Db_Expr('MAX(sort_order)')))
                        ->where('rule_id != ?', $row['rule_id']);
                    $maxPos = $db->fetchOne($select);  
                    
                    if (is_null($maxPos)) // it is already the last item
                        continue;                    
                    
                    if ($maxPos >= 4294967295)  { // I'm paranoic :)
                        $this->_fault($this->__('Can not move rule with id=%s.', $id));
                        continue;
                    }  
                    if ($row['sort_order'] <= $maxPos){  
                        $db->update($table, array('sort_order'=>$maxPos+1), 
                            'rule_id =' . intval($row['rule_id']));
                        ++$num;    
                    }             
                }
                
            } 
            catch (Exception $e) {
                $this->_fault($e->getMessage(), false);
            }   
        }
         $this->_success($this->__('Total of %d rule(s) have been moved.', $num));

         $redirect_URl=explode(Mage::getBaseUrl(),Mage::helper('core/http')->getHttpReferer()); 

         if (strpos($redirect_URl[1], 'admin') !== false) 
           {
            $newredirectURL=explode("admin/",$redirect_URl[1]);
        
            return $this->_redirect('adminhtml/'.$newredirectURL[1]);
             
           }

           if (strpos($redirect_URl[1], 'admin') == false) 
           {
            $this->_redirect( $redirect_URl[1]);
             return true;
           }
    }    
    
    public function massDeleteAction()
    {

        $redirect_URl=explode(Mage::getBaseUrl(),Mage::helper('core/http')->getHttpReferer());        
       
        $idss = $this->getRequest()->getParam('rules');
         $ids = explode(',', $idss);
      if (!is_array($ids)) {

       return $this->_fault($this->__('Please select rule(s).'));
        }
      
        $num = 0;
        foreach ($ids as $id) {
            try {
                $rule = Mage::getSingleton('salesrule/rule')->load($id);
               
                $rule->getPrimaryCoupon()->delete();
                $rule->delete();
                ++$num;
            } 
            catch (Exception $e) {
                $this->_fault($e->getMessage(), false);
            }   
        }
       
       
         $this->_success($this->__('Total of %d record(s) have been deleted.', $num));

         if (strpos($redirect_URl[1], 'admin') !== false) 
           {
            $newredirectURL=explode("admin/",$redirect_URl[1]);
        
            return $this->_redirect('adminhtml/'.$newredirectURL[1]);
             
           }

           if (strpos($redirect_URl[1], 'admin') == false) 
           {
            $this->_redirect( $redirect_URl[1]);
             return true;
           }
    }
  
    public function massEnableAction()
    {
        
         $this->_modifyStatus(1);
        
        $redirect_URl=explode(Mage::getBaseUrl(),Mage::helper('core/http')->getHttpReferer()); 

         if (strpos($redirect_URl[1], 'admin') !== false) 
           {
            $newredirectURL=explode("admin/",$redirect_URl[1]);
        
            return $this->_redirect('adminhtml/'.$newredirectURL[1]);
             
           }

           if (strpos($redirect_URl[1], 'admin') == false) 
           {
            $this->_redirect( $redirect_URl[1]);
             return true;
           }
    }
    
    public function massDisableAction()
    {
         $this->_modifyStatus(0);

         $redirect_URl=explode(Mage::getBaseUrl(),Mage::helper('core/http')->getHttpReferer()); 
        
         if (strpos($redirect_URl[1], 'admin') !== false) 
           {
            $newredirectURL=explode("admin/",$redirect_URl[1]);
        
            return $this->_redirect('adminhtml/'.$newredirectURL[1]);
             
           }

           if (strpos($redirect_URl[1], 'admin') == false) 
           {
            $this->_redirect( $redirect_URl[1]);
             return true;
           }
    }         
      
    protected function _modifyStatus($status)
    {
        $idss = $this->getRequest()->getParam('rules');
         $ids = explode(',', $idss);
        if (!is_array($ids)) {
            return $this->_fault($this->__('Please select rule(s).'));
        }
        
        $num = 0;
        foreach ($ids as $id) {
            try {
                $rule = Mage::getModel('salesrule/rule')->load($id);
                if ($rule->getIsActive() != $status){
                    $rule->setIsActive($status);
                    $rule->save();
                    ++$num;
                }
            } 
            catch (Exception $e) {
                $this->_fault($e->getMessage(), false);
            }   
        }
         $this->_success($this->__('Total of %d record(s) have been updated.', $num));

          $redirect_URl=explode(Mage::getBaseUrl(),Mage::helper('core/http')->getHttpReferer()); 
        
         if (strpos($redirect_URl[1], 'admin') !== false) 
           {
            $newredirectURL=explode("admin/",$redirect_URl[1]);
        
            return $this->_redirect('adminhtml/'.$newredirectURL[1]);
             
           }

           if (strpos($redirect_URl[1], 'admin') == false) 
           {
            $this->_redirect( $redirect_URl[1]);
             return true;
           }
    }    
      
    protected function _fault($message, $redirect=true)
    {
        $this->_getSession()->addError($message);
        if ($redirect)
            $this->_redirect($this->gridUrl);
            
        return $this;
    }
    
    protected function _success($message, $redirect=true)
    {
        $this->_getSession()->addSuccess($message);
        if ($redirect)
            $this->_redirect($this->gridUrl);
            
        return $this;
    }    

    

    
   

    public function newActionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('salesrule/rule'))
            ->setPrefix('actions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    public function applyRulesAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->_initRule()->loadLayout()->renderLayout();
    }

    /**
     * Coupon codes grid
     */
    public function couponsGridAction()
    {
        $this->_initRule();
        $this->loadLayout()->renderLayout();
    }

    /**
     * Export coupon codes as excel xml file
     *
     * @return void
     */
    public function exportCouponsXmlAction()
    {
        $this->_initRule();
        $rule = Mage::registry('current_promo_quote_rule');
        if ($rule->getId()) {
            $fileName = 'coupon_codes.xml';
            $content = $this->getLayout()
                ->createBlock('adminhtml/promo_quote_edit_tab_coupons_grid')
                ->getExcelFile($fileName);
            $this->_prepareDownloadResponse($fileName, $content);
        } else {
            $this->_redirect('*/*/detail', array('_current' => true));
            return;
        }
    }

    /**
     * Export coupon codes as CSV file
     *
     * @return void
     */
    public function exportCouponsCsvAction()
    {
        $this->_initRule();
        $rule = Mage::registry('current_promo_quote_rule');
        if ($rule->getId()) {
            $fileName = 'coupon_codes.csv';
            $content = $this->getLayout()
                ->createBlock('adminhtml/promo_quote_edit_tab_coupons_grid')
                ->getCsvFile();
            $this->_prepareDownloadResponse($fileName, $content);
        } else {
            $this->_redirect('*/*/detail', array('_current' => true));
            return;
        }
    }

    /**
     * Coupons mass delete action
     */
    public function couponsMassDeleteAction()
    {
        $this->_initRule();
        $rule = Mage::registry('current_promo_quote_rule');

        if (!$rule->getId()) {
            $this->_forward('noRoute');
        }

        $codesIds = $this->getRequest()->getParam('ids');

        if (is_array($codesIds)) {

            $couponsCollection = Mage::getResourceModel('salesrule/coupon_collection')
                ->addFieldToFilter('coupon_id', array('in' => $codesIds));

            foreach ($couponsCollection as $coupon) {
                $coupon->delete();
            }
        }
    }

    /**
     * Generate Coupons action
     */
    public function generateAction()
    {
        if (!$this->getRequest()->isAjax()) {
            $this->_forward('noRoute');
            return;
        }
        $result = array();
        $this->_initRule();

        /** @var $rule Mage_SalesRule_Model_Rule */
        $rule = Mage::registry('current_promo_quote_rule');

        if (!$rule->getId()) {
            $result['error'] = Mage::helper('salesrule')->__('Rule is not defined');
        } else {
            try {
                $data = $this->getRequest()->getParams();
                if (!empty($data['to_date'])) {
                    $data = array_merge($data, $this->_filterDates($data, array('to_date')));
                }

                /** @var $generator Mage_SalesRule_Model_Coupon_Massgenerator */
                $generator = $rule->getCouponMassGenerator();
                if (!$generator->validateData($data)) {
                    $result['error'] = Mage::helper('salesrule')->__('Not valid data provided');
                } else {
                    $generator->setData($data);
                    $generator->generatePool();
                    $generated = $generator->getGeneratedCount();
                    $this->_getSession()->addSuccess(Mage::helper('salesrule')->__('%s Coupon(s) have been generated', $generated));
                    $this->_initLayoutMessages('vendors/session');
                    $result['messages']  = $this->getLayout()->getMessagesBlock()->getGroupedHtml();
                }
            } catch (Mage_Core_Exception $e) {
                $result['error'] = $e->getMessage();
            } catch (Exception $e) {
                $result['error'] = Mage::helper('salesrule')->__('An error occurred while generating coupons. Please review the log and try again.');
                Mage::logException($e);
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Chooser source action
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $chooserBlock = $this->getLayout()->createBlock('adminhtml/promo_widget_chooser', '', array(
            'id' => $uniqId
        ));
        $this->getResponse()->setBody($chooserBlock->toHtml());
    }

    /**
     * Returns result of current user permission check on resource and privilege
     * @return boolean
     */
    // protected function _isAllowed()
    // {
    //     return Mage::getSingleton('admin/session')->isAllowed('promo/quote');
    // }
}