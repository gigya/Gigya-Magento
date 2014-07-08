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
        $value = $this->getValue();
        if ($value == 'raas') {
            $helper = Mage::helper('Gigya_Social');
            if (!$helper->utils->isRaaS()) {
                Mage::throwException(Mage::helper('adminhtml')->__("Gigya's Registration-as-a-Service (RaaS) is currently not part of your site package.
            Please contact your Gigya account manager to activate the package."));
            } else {
                $data = $this->getData();
                $raasConfig = $data['gigya_raas_conf']['fields'];
                if (!$this->checkNotEmptyRaas($raasConfig)) {
                    Mage::throwException(Mage::helper('adminhtml')->__("Screen set IDs can not be empty"));
                }
            }
        }

    }

    private function checkNotEmptyRaas($config)
    {
        $required = array('WebScreen', 'MobileScreen', 'LoginScreen', 'RegisterScreen', 'ProfileWebScreen', 'ProfileMobileScreen');
        foreach ($required as $field) {
            if (empty($config[$field]['value'])) {
                return false;
            }
       }
       return true;
    }


} 