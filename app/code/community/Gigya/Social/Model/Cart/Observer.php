<?php

/**
 * Class Gigya_Social_Model_Cart_Observer
 * @author  Yaniv Aran-Shamir
 */
class Gigya_Social_Model_Cart_Observer
{
    protected $helper;

    function __construct()
    {
        $this->userMod = Mage::getStoreConfig('gigya_login/gigya_user_management/login_modes');
        $this->helper = Mage::helper('Gigya_Social');
    }

    public function addShareUi($observer)
    {
        if ($this->helper->isShareActionEnabled('cart')) {
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
        if ($this->helper->isShareBarEnabled('order')) {
            Mage::getSingleton('checkout/session')->setData('gigyaShareOrder', array('pid' => $pid));
        }
        if ($this->helper->isShareActionEnabled('order')) {
            Mage::getSingleton('checkout/session')->setData('gigyaShare', array('pid' => $pid, 'op' => 'order'));
        }
    }

    public function incCounters($observer)
    {
        $cust_session = Mage::getSingleton('customer/session');
        $gigyaAccount = $cust_session->getData('gigyaAccount');
        $gigyaUid = $gigyaAccount['UID'];
        if($this->helper->isCountersEnabled()) {
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
            $res = $this->helper->utils->incrementCounter($gigyaUid, $counters);
        }
    }

    public function syncPurchaseToGigya($observer)
    {
        $cust_session = Mage::getSingleton('customer/session');
        $gigyaAccount = $cust_session->getData('gigyaAccount');
        $gigyaUid = $gigyaAccount['UID'];
        if($this->helper->isCountersEnabled()) {
            $orderIds = $observer->getEvent()->getOrderIds();
            if (empty($orderIds) || !is_array($orderIds)) {
                return;
            }
            $order = Mage::getModel('sales/order')->load(reset($orderIds));
            $gData = $this->orderToGigyaData($order);
            $params = array(
                'UID' => $gigyaUid,
                'data' => json_encode(array('reviewReminder' => $gData))
            );
            Mage::dispatchEvent('gigya_pre_review_reminder', array(
                    'params' => $params,
                    'order' => $order
                ));
            $this->helper->utils->call('accounts.setAccountInfo', $params);
        }
    }

    public function notifyGmAction($observer)
    {
        if ($this->helper->isPluginEnabled('gigya_gamification/gigya_gamification_conf') && $this->helper->isGmNotifyActionEnabled()) {
            $cust_session = Mage::getSingleton('customer/session');
            $gigyaAccount = $cust_session->getData('gigyaAccount');
            $gigyaUid = $gigyaAccount['UID'];
            $params = array(
                'UID' => $gigyaUid,
                'action' => 'purchase'
            );
            $this->helper->utils->call("gm.notifyAction", $params);
        }
    }

    private function orderToGigyaData($order)
    {
        // Convert order to gigya data object.
        $items = $order->getAllItems();
        $gigya_data = array();
        $num_of_items = (count($items) < 10 ) ? count($items) : 10;
        for ($i = 0; $i <= $num_of_items - 1; $i++) {
            $gigya_data['item_' . ($i + 1) . '_reviewUrl'] = $this->getReviewUrl($items[$i]->getProductId());
            $gigya_data['item_' . ($i + 1) . '_qty'] = $items[$i]->getQtyToInvoice();
            $gigya_data['item_' . ($i + 1) . '_price'] = $items[$i]->getPriceInclTax();
            $gigya_data['item_' . ($i + 1) . '_name'] = $items[$i]->getName();
            $gigya_data['item_' . ($i + 1) . '_total_price'] = $items[$i]->getRowTotalInclTax();
        }
        $gigya_data['purchase_timestamp'] = $_SERVER['REQUEST_TIME'];
        return $gigya_data;

    }


    private  function getReviewUrl($productId)
    {
        return Mage::getUrl('review/product/list', array('id' => $productId));
    }

}


