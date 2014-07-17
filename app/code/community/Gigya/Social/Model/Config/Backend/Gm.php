<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 6/19/14
 * Time: 4:04 PM
 */
class Gigya_Social_Model_Config_Backend_Gm extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if ($value == 1) {
            $helper = Mage::helper('Gigya_Social');
            if (!$helper->utils->isGm()) {
                Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('adminhtml')->__("Gamification is a premium Gigya service that is not part of your site package.
                Please contact your Gigya account manager if you wish to activate this feature."));
            }
        }

    }


} 