<?php
/**
 * Class Gigya_Social_Model_Cart_Observer
 * @author  Yaniv Aran-Shamir
 */
class Gigya_Social_Model_Cart_Observer
{
  public function addShareUi($observer)
  {
    $productId = $observer->getProduct()->getId();
    Mage::getSingleton('checkout/session')->setData('gigyaShare', $productId);
  }

  public function addOrderShareUi(Varien_Event_Observer $observer)
  {
    $order = $observer->getEvent()->getOrder();
    $items = $order->getAllItems();
    try {
    reset($items);
    $pid = key($items);
    Mage::log(var_export($items));
    Mage::getSingleton('checkout/session')->setData('gigyaShare', $pid);
      }
      catch (Exception $e) {
       $code = $e->getCode();
       $message = $e->getMessage();
       return $message;
      }
  }

}


