<?php

/**
 * Class Gigya_Social_Model_Cart_Observer
 * @author  Yaniv Aran-Shamir
 */
class Gigya_Social_Model_Cart_Observer
{
    public function addShareUi($observer)
    {
        if (Mage::helper('Gigya_Social')->isShareActionEnabled('cart')) {
            $productId = $observer->getProduct()->getId();
            Mage::getSingleton('checkout/session')->setData('gigyaShare', array('pid' => $productId, 'op' => 'cart'));
        }
    }

    public function addOrderShareUi(Varien_Event_Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $order = Mage::getModel('sales/order')->load(reset($orderIds));
        $items = $order->getAllItems();
        $prod = reset($items);
        $pid = $prod->getProductId();
        if (Mage::helper('Gigya_Social')->isShareBarEnabled('order')) {
            Mage::getSingleton('checkout/session')->setData('gigyaShareOrder', array('pid' => $pid));
        }
        if (Mage::helper('Gigya_Social')->isShareActionEnabled('order')) {
            Mage::getSingleton('checkout/session')->setData('gigyaShare', array('pid' => $pid, 'op' => 'order'));
        }
    }

    public function incCounters($observer)
    {
        $cust_session = Mage::getSingleton('customer/session');
        $gigyaAccount = $cust_session->getData('gigyaAccount');
        $gigyaUid = $gigyaAccount['UID'];
        $helper = Mage::helper('Gigya_Social');
        if($helper->isCountersEnabled()) {
            $orderIds = $observer->getEvent()->getOrderIds();
            if (empty($orderIds) || !is_array($orderIds)) {
                return;
            }
            $order = Mage::getModel('sales/order')->load(reset($orderIds));
            $counters = array();
            $counter = new stdClass();
            $counter->class = "_purchases";
            $counter->path = "/";
            $counter->count = (int) $order->getTotalQtyOrdered();
            $counter->value = $order->getGrandTotal();
            Mage::dispatchEvent('gigya_counter_pre_send', array('counter' => $counter));
            $counters[] = $counter;
            $helper->utils->incrementCounter($gigyaUid, $counters);
        }
    }

}


