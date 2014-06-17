<?php

/**
 * Class Gigya_Social_Modle_Customer_Observer
 * @author
 */
class Gigya_Social_Model_Customer_Observer
{
    protected $userMod;

    public function __constract()
    {
        $this->userMod = Mage::getStoreConfig('gigya_login/gigya_user_management/login_modes');
    }

    public function notify_registration($observer)
    {
        if ($this->userMod == 'social') {
            $customer_data = $observer['customer']->getData();
            $id = $customer_data['entity_id'];
            if (!empty($customer_data['gigyaUser'])) {
                Mage::helper('Gigya_Social')->notifyRegistration($customer_data['gigyaUser']['UID'], $id);
            } else {
                Mage::helper('Gigya_Social')->notifyLogin($id, 'true');
                Mage::getSingleton('customer/session')->setSuppressNoteLogin(TRUE);
            }
        }
    }

    public function notify_delete($observer)
    {
        $id = $observer->getEvent()->getCustomer()->getId();
        Mage::helper('Gigya_Social')->deleteAccount($id);
    }

    public function notify_login($observer)
    {

        if ($this->userMod == 'social') {
            Mage::log(Mage::getSingleton('customer/session')->getSuppressNoteLogin());
            if (!Mage::getSingleton('customer/session')->getSuppressNoteLogin()) {
                $action = Mage::getSingleton('customer/session')->getData('gigyaAction');
                $id = $observer->getEvent()->getCustomer()->getId();
                $gigya_uid = Mage::getSingleton('customer/session')->getData('gigyaUid');
                if (!empty($action)) {
                    if ($action === 'linkAccount' && !empty($gigya_uid)) {
                        Mage::helper('Gigya_Social')->notifyRegistration($gigya_uid, $id);
                    }
                } else {
                    $magInfo = $observer->getEvent()->getCustomer()->getData();
                    $userInfo = array(
                        'firstName' => $magInfo['firstname'],
                        'lastName' => $magInfo['lastname'],
                        'email' => $magInfo['email'],
                    );
                    Mage::helper('Gigya_Social')->notifyLogin($id, 'false', $userInfo);
                }
            } else {
                Mage::getSingleton('customer/session')->unsSuppressNoteLogin();
            }
        }
    }

    public function notify_logout($observer)
    {
        $id = $observer->getEvent()->getCustomer()->getId();
        Mage::getSingleton('core/session')->setData('logout', 'true');
        Mage::helper('Gigya_Social')->notifyLogout($id);
    }
}

