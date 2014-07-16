<?php
/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 6/19/14
 * Time: 4:04 PM
 */

class Gigya_Social_Model_Config_Backend_ApiKey extends Mage_Core_Model_Config_Data {
    protected function _beforeSave()
    {
        $value = $this->getValue();
        $helper = Mage::helper('Gigya_Social');
        $helper->utils->setApiKey($value);
        $data = $this->getData();
        $secret = $data['fieldset_data']['secretkey'];
        $dataCenter = $data['fieldset_data']['dataCenter'];
        $userKey = $data['fieldset_data']['userKey'];
        $userSecret = $data['fieldset_data']['userSecret'];
        $useUserKey = $data['fieldset_data']['useUserKey'];
        $helper->utils->setApiDomain($dataCenter);
        if ($useUserKey) {
            if(empty($useUserKey)){
                Mage::throwException(Mage::helper('adminhtml')->__("Gigya user key is required."));
            }
            if (empty($userSecret)){
                Mage::throwException(Mage::helper('adminhtml')->__("Gigya user secret is required."));

            }
            $helper->utils->setUserKey($userKey);
            $helper->utils->setUserSecret($userSecret);
            $helper->utils->setUseUserKey($useUserKey);
        } else {
            $helper->utils->setApiSecret($secret);
        }
        if (!$helper->utils->isApiKeyValid()) {
            Mage::throwException(Mage::helper('adminhtml')->__("Gigya Api Key is not valid"));
        }

    }


} 