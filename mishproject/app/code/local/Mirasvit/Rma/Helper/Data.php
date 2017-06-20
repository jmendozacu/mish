<?php
class Mirasvit_Rma_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getConfig()
    {
        return Mage::getSingleton('rma/config');
    }

    public function toAdminUserOptionArray($emptyOption = false) {
        $arr = Mage::getModel('admin/user')->getCollection()->toArray();
        $result = array();
        foreach ($arr['items'] as $value) {
            $result[] = array('value'=>$value['user_id'], 'label' => $value['firstname'].' '.$value['lastname']);
        }
        if ($emptyOption) {
            array_unshift($result, array('value' => 0, 'label' => Mage::helper('rma')->__('-- Please Select --')));
        }
        return $result;
    }
    public function getAdminUserOptionArray($emptyOption = false) {
        $arr = Mage::getModel('admin/user')->getCollection()->toArray();
        $result = array();
        foreach ($arr['items'] as $value) {
            $result[$value['user_id']] = $value['firstname'].' '.$value['lastname'];
        }
        if ($emptyOption) {
            $result[0] = Mage::helper('rma')->__('-- Please Select --');
        }
        return $result;
    }

    /************************/

    public function getOrderLabel($order, $url = false)
    {
        if (!is_object($order)) {
            $order = Mage::getModel('sales/order')->load($order);
        }
        $res = "#{$order->getRealorderId()}";
        if ($url) {
            $res = "<a href='{$url}' target='_blank'>$res</a>";
        }
        $res .= Mage::helper('rma')->__(" at %s (%s)",
            Mage::helper('core')->formatDate($order->getCreatedAt(), 'medium'),
            strip_tags($order->formatPrice($order->getGrandTotal()))
        );
        return $res;
    }

    public function getOrderItemLabel($item)
    {
        $name = $item->getName();
        if (!$name && is_object($item->getProduct())) { //old versions support
            $name = $item->getProduct()->getName();
        }
        $options = $this->getItemOptions($item);
        if (count($options)) {
            $name .= ' (';
            foreach ($options as $option) {
                $name .= $option['label'].': '.$option['value'].', ';
            }
            $name = substr($name, 0, -2); //remove last ,
            $name .= ')';
        }

        return $name;
    }

    public function getItemOptions($orderItem)
    {
        $result = array();
        if ($options = $orderItem->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }


    public function generateIncrementId($rma)
    {
        $maxLen = 9;
        $id = (string)$rma->getId();
        $storeId = (string)$rma->getStoreId();

        $totalLen = strlen($id) + strlen($storeId);

        return $storeId. str_repeat('0', $maxLen - $totalLen).$id;
    }

    protected function _convertRmaItem($rma, $item)
    {
        if (!$rma || !$rma->getId()) {
            $item = Mage::getModel('rma/item')->initFromOrderItem($item);
        }
        return $item;
    }

    public function getRmaItems($rma = null, $order = null)
    {
        $items = array();
        if ($rma) {
            $order = $rma->getOrder();
        }
        if ($rma && $rma->getId()) {
            $collection = Mage::getModel('rma/item')->getCollection()
                ->addFieldToFilter('rma_id', $rma->getId());
        } else {
            $collection = $order->getItemsCollection();

        }
        foreach ($collection as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            if ($item->getProductType() == 'bundle') {
                $items[] = $this->_convertRmaItem($rma, $item);
                foreach ($item->getChildrenItems() as $bundleItem) {
                    $bundleItem = $this->_convertRmaItem($rma, $bundleItem);
                    $bundleItem->setIsBundleItem(true);
                    $items[] = $bundleItem;
                }
            } else {
                $item = $this->_convertRmaItem($rma, $item);
                $items[] = $item;
            }
        }
        return $items;
    }

    public function convertToHtml($text)
    {
        $html =  nl2br($text);
        return $html;
    }

    public function getNewRmaGuestUrl()
    {
        return Mage::getUrl('rma/guest/new');
    }

    public function getStatusCollection()
    {
        $collection = Mage::getModel('rma/status')->getCollection()
            ->addFieldToFilter('is_active', true);
        return $collection;
    }

    public function copyEmailAttachments($email, $comment)
    {
        foreach ($email->getAttachments() as $emailAttachment) {
            Mage::getModel('mstcore/attachment')
                ->setEntityId($comment->getId())
                ->setEntityType('COMMENT')
                ->setName($emailAttachment->getName())
                ->setSize($emailAttachment->getSize())
                ->setBody($emailAttachment->getBody())
                ->setType($emailAttachment->getType())
                ->save();
        }
    }

    public function getReturnPeriod()
    {
        return  $this->getConfig()->getGeneralReturnPeriod();
    }

    public function getLastReturnGmtDate()
    {
        $time = gmdate('U') - $this->getReturnPeriod() * 24 * 60 * 60;
        return Mage::getSingleton('core/date')->gmtDate(null, $time);
    }

    public function getStatusByCode($code)
    {
        $collection = Mage::getModel('rma/status')->getCollection();
        $collection->addFieldToFilter('code', $code);
        if ($collection->count()) {
            return $collection->getFirstItem();
        }
    }

}