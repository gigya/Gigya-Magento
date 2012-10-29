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
}


