<?php
/**
 * Class Gigya_Social_Modle_Customer_Observer
 * @author
 */
class Gigya_Social_Model_Customer_Observer
{
  public function notify_registration($observer)
  {
    $customer_data = $observer['customer']->getData();
    if (!empty($customer_data['gigyaUser'])) {
      Mage::helper('Gigya_Social')->notifyRegistration($customer_data['gigyaUser']['UID'], $customer_data['entity_id']);
    }
  }

  public function notify_delete($observer)
  {
    $id = $observer->getEvent()->getCustomer()->getId();
    Mage::helper('Gigya_Social')->deleteAccount($id);
  }

  public function notify_login($observer)
  {
    $action = Mage::getSingleton('customer/session')->getData('gigyaAction');
    $id = $observer->getEvent()->getCustomer()->getId();
    $gigya_uid = Mage::getSingleton('customer/session')->getData('gigyaUid');
    if (!empty($action)) {
      if ($action == 'login') {
        Mage::helper('Gigya_Social')->notifyLogin($id);
      }
      elseif ($action === 'linkAccount' && !empty($gigya_uid)) {
        Mage::helper('Gigya_Social')->notifyRegistration($gigya_uid, $id);
      }
    }
  }

  public function notify_logout($observer)
  {
    $id = $observer->getEvent()->getCustomer()->getId();
    Mage::helper('Gigya_Social')->notifyLogout($id);
  }






}

