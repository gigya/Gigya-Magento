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

}


