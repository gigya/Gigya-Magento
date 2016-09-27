<?php

/**
 * Class Gigya_Social_Modle_Customer_Observer
 * Event Observers registered in config.xml
 */
class Gigya_Social_Model_Customer_Observer
{

    protected $userMod;
    protected $helper;
    protected $lead;

    function __construct()
    {
        $this->userMod = Mage::getStoreConfig('gigya_login/gigya_user_management/login_modes');
        $this->lead    = Mage::getStoreConfig('gigya_login/gigya_raas_conf/session_lead');
        $this->helper  = Mage::helper('Gigya_Social');
    }

    public function notify_registration($observer)
    {
        if ($this->userMod == 'social') {
            $customer_data = $observer['customer']->getData();
            $id            = $customer_data['entity_id'];
            if (!empty($customer_data['gigyaUser'])) {
                $this->helper->notifyRegistration($customer_data['gigyaUser']['UID'], $id);
            } else {
                $this->helper->notifyLogin($id, 'true');
                Mage::getSingleton('customer/session')->setSuppressNoteLogin(
                    true
                );
            }
        }
    }

    public function notify_delete($observer)
    {

        $helper        = Mage::helper('Gigya_Social');
        $this->userMod = Mage::getStoreConfig(
            'gigya_login/gigya_user_management/login_modes'
        );
        if ($this->userMod == 'social') {
            $id = $observer->getEvent()->getCustomer()->getId();
            $this->helper->deleteAccount($id);
        } elseif ($this->userMod == 'raas') {
            $cust     = $observer->getEvent()->getCustomer();
            $gigyaUid = $cust->getData('gigya_uid');
            if (!empty($gigyaUid)) {
                $helper->utils->deleteAccountByGUID($gigyaUid);
            }
        }
    }

    public function notify_login($observer)
    {
        if ($this->userMod == 'social') {
            Mage::log(
                Mage::getSingleton('customer/session')->getSuppressNoteLogin()
            );
            if (!Mage::getSingleton('customer/session')->getSuppressNoteLogin()
            ) {
                $action    = Mage::getSingleton('customer/session')->getData(
                    'gigyaAction'
                );
                $id        = $observer->getEvent()->getCustomer()->getId();
                $gigya_uid = Mage::getSingleton('customer/session')->getData(
                    'gigyaUid'
                );
                if (!empty($action)) {
                    if ($action === 'linkAccount' && !empty($gigya_uid)) {
                        $this->helper->notifyRegistration($gigya_uid, $id);
                    }
                } else {
                    $magInfo  = $observer->getEvent()->getCustomer()->getData();
                    $userInfo = array(
                        'firstName' => $magInfo['firstname'],
                        'lastName'  => $magInfo['lastname'],
                        'email'     => $magInfo['email'],
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
        } else {
            if ($this->userMod == 'raas') {
                $id = $observer->getEvent()->getCustomer()->getData(
                    'gigya_uid'
                );
                // gigya_uid does not get passed in in observer. options:
                // add gigya_uid to observer
                // make sure gigya_uid is saved to magento customer data, and pull it from magento customer
                // make the logout  happen from browser side
                $params = array('UID' => $id);
                $this->helper->utils->call('accounts.logout', $params);
            }
        }
    }

    /*
     * @var Varien_Event_Observer
     */
    public function syncToGigya($observer)
    {
        if ("raas" == $this->userMod) {
            $customer   = $observer->getEvent()->getCustomer();
            $attributes = $customer->getData();
            $uid        = $attributes['gigya_uid'];
            $updater    = new Gigya_Social_Helper_FieldMapping_GigyaUpdater(
                $attributes, $uid
            );
            $updater->updateGigya();
        }
    }

    /*
     * @var Varien_Event_Observer $observer
     */
    public function convertGenderFromGigya($observer)
    {
        if ("raas" == $this->userMod) {
            /** @var Gigya_Social_Helper_FieldMapping_MagentoUpdater $updater */
            $updater      = $observer->getData("updater");
            $gigyaAccount = $updater->getGigyaAccount();
            if (isset($gigyaAccount['profile']['gender'])) {
                $gen                               = $this->genderConvert(
                    "g2cms", null, $gigyaAccount['profile']['gender']
                );
                $gigyaAccount['profile']['gender'] = null == $gen ? 0 : $gen;
                $updater->setGigyaAccount($gigyaAccount);
            }
        }
    }

    public function genderConvert($direction, $cmsVal, $gigyaVal)
    {
        if (Mage::getEdition() == Mage::EDITION_ENTERPRISE) {
            $mapping = array(
                "m" => 123,
                "f" => 124,
                "u" => 0
            );
        } elseif (Mage::getEdition() == Mage::EDITION_COMMUNITY) {
            $mapping = array(
                "m" => 1,
                "f" => 2,
                "u" => 0
            );
        }
        if ("g2cms" == $direction) {
            return isset($mapping[$gigyaVal]) ? $mapping[$gigyaVal] : null;
        }
        if ("cms2g" == $direction) {
            $fliped = array_flip($mapping);
            return isset($fliped[$cmsVal]) ? $fliped[$cmsVal] : null;
        }
        return null;
    }

    public function convertGenderToGigya($observer)
    {
        if ("raas" == $this->userMod) {
            /** @var Gigya_Social_Helper_FieldMapping_GigyaUpdater $updater */
            $updater            = $observer->getData("updater");
            $cmsArray           = $updater->getCmsArray();
            $gen                = $this->genderConvert(
                "cms2g", $cmsArray['gender'], null
            );
            $cmsArray['gender'] = null == $gen ? 'u' : $gen;
            $updater->setCmsArray($cmsArray);
        }
    }

    public function reLoginToGigya($observer)
    {
        if ("raas" == $this->userMod && 'magento' == $this->lead) {
            /** @var Mage_Customer_Model_Session $session */
            $session = $observer->getData('customer_session');
            if($session->isLoggedIn() && !$this->isGigyaLoggedIn()) {
                /** @var Mage_Customer_Model_Customer $customer */
                $customer = $session->getCustomer();
                $gigyaUid = $customer->getData('gigya_uid');
                $this->doReLoginToGigya($gigyaUid);
            }
        }

    }

    private function isGigyaLoggedIn()
    {
        $apiKey = Mage::getStoreConfig('gigya_global/gigya_global_conf/apikey');
        return isset($_COOKIE['glt_' . $apiKey]);
    }

    private function doReLoginToGigya($uid)
    {
        $expTime = $this->getExpirationTime();
        return $this->helper->accountsNotifyLogin($uid, $expTime, 'cms relogin');

    }

    private function getExpirationTime()
    {
        $advanced_config = Mage::getStoreConfig('gigya_global/gigya_global_conf/advancedConfig');
        if($advanced_config !== '') {
            $advanced_config_arr = Mage::helper('Gigya_Social')->getGigGlobalAdvancedConfig($advanced_config);
            if(!$advanced_config_arr) {
                $advanced_config_arr = Mage::helper('Gigya_Social')->_confStringToArry($advanced_config);
            }
            if(!empty($advanced_config_arr['sessionExpiration'])) {
                $sessionExpiration = $advanced_config_arr['sessionExpiration'];
            } else {
                $sessionExpiration = Mage::getStoreConfig(Mage_Core_Model_Cookie::XML_PATH_COOKIE_LIFETIME);
            }
            return (null == $sessionExpiration) ? 0 : $sessionExpiration;
        }


    }
}

