<?php

/**
 * Class Gigya_Social_Modle_Customer_Observer
 * Event Observers registered in config.xml
 */
class Gigya_Social_Model_Customer_Observer
{
    protected $userMod;
    protected $helper;


    function __construct()
    {
        $this->userMod = Mage::getStoreConfig('gigya_login/gigya_user_management/login_modes');
        $this->helper = Mage::helper('Gigya_Social');
    }


    public function notify_registration($observer)
    {
        if ($this->userMod == 'social') {
            $customer_data = $observer['customer']->getData();
            $id = $customer_data['entity_id'];
            if (!empty($customer_data['gigyaUser'])) {
                $this->helper->notifyRegistration($customer_data['gigyaUser']['UID'], $id);
            } else {
                $this->helper->notifyLogin($id, 'true');
                Mage::getSingleton('customer/session')->setSuppressNoteLogin(TRUE);
            }
        }
    }

    public function notify_delete($observer)
    {

        $helper = Mage::helper('Gigya_Social');
        $this->userMod = Mage::getStoreConfig('gigya_login/gigya_user_management/login_modes');
        if ($this->userMod == 'social') {
            $id = $observer->getEvent()->getCustomer()->getId();
            $this->helper->deleteAccount($id);
        } elseif ($this->userMod == 'raas') {
            $cust = $observer->getEvent()->getCustomer() ;
            $gigyaUid = $cust->getData('gigya_uid');
            if (!empty($gigyaUid)) {
                $helper->utils->deleteAccountByGUID($gigyaUid);
            }
        }
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
                        $this->helper->notifyRegistration($gigya_uid, $id);
                    }
                } else {
                    $magInfo = $observer->getEvent()->getCustomer()->getData();
                    $userInfo = array(
                        'firstName' => $magInfo['firstname'],
                        'lastName' => $magInfo['lastname'],
                        'email' => $magInfo['email'],
                    );
                    $this->helper->notifyLogin($id, 'false', $userInfo);
                }
            } else {
                Mage::getSingleton('customer/session')->unsSuppressNoteLogin();
            }
        }
    }

    /*
     * Observer func for Magento customer_logout event
     * Handles log out from gigya when magento customer logs out
     * @param Varien_Event_Observer $observer
     */
    public function notify_logout($observer)
    {
        if ($this->userMod == 'social') {
            $id = $observer->getEvent()->getCustomer()->getId();
            Mage::getSingleton('core/session')->setData('logout', 'true');
            $this->helper->notifyLogout($id);
        } else if ($this->userMod == 'raas') {
            $id = $observer->getEvent()->getCustomer()->getData('gigya_uid');
            // gigya_uid does not get passed in in observer. options:
                // add gigya_uid to observer
                // make sure gigya_uid is saved to magento customer data, and pull it from magento customer
                // make the logout  happen from browser side
            $params = array('UID' => $id);
            $this->helper->utils->call('accounts.logout', $params);
        }
    }

    /*
     * @var Varien_Event_Observer
     */
    public function syncToGigya($observer)
    {
        if ("raas" == $this->userMod) {
            $customer = $observer->getEvent()->getCustomer();
            $attributes = $customer->getData();
            $uid = $attributes['gigya_uid'];
            Mage::dispatchEvent("pre_sync_to_gigya", array("magento_attributes" => $attributes, "customer" => $customer));
            $updater = new Gigya_Social_Helper_FieldMapping_GigyaUpdater($attributes, $uid);
            $updater->updateGigya();
        }
    }
}

