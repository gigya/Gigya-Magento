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
        if (!$helper->utils->isApiKeyValid()) {
            Mage::throwException(Mage::helper('adminhtml')->__("Gigya Api Key is not valid"));
        }

    }


} 