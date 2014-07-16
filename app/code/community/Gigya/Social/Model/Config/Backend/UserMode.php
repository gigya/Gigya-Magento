<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 6/19/14
 * Time: 4:04 PM
 */
class Gigya_Social_Model_Config_Backend_UserMode extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        $helper = Mage::helper('Gigya_Social');
        $value = $this->getValue();
        if ($value == 'raas') {
            if (!$helper->utils->isRaaS()) {
                Mage::throwException(Mage::helper('adminhtml')->__("Gigya's Registration-as-a-Service (RaaS) is currently not part of your site package.
            Please contact your Gigya account manager to activate the package."));
            } else {
                if (!$this->checkNotEmptyRaas()) {
                    Mage::throwException(Mage::helper('adminhtml')->__("Screen set IDs can not be empty"));
                }
            }
        } elseif ($value == 'social') {
            Mage::throwException("This site is configured on Gigya server to use Registration-as-a-Service.
                     Please contact your Gigya account manager for migration instruction");

        }
    }

    private function checkNotEmptyRaas()
    {
        $data = $this->getData();
        $raasConfig = $data['groups']['gigya_raas_conf']['fields'];
        $required = array('WebScreen', 'MobileScreen', 'LoginScreen', 'RegisterScreen', 'ProfileWebScreen', 'ProfileMobileScreen');
        foreach ($required as $field) {
            if (empty($raasConfig[$field]['value'])) {
                return false;
            }
       }
       return true;
    }


} 