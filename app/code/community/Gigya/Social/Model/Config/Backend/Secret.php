<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 5/22/16
 * Time: 12:49 PM
 */
class Gigya_Social_Model_Config_Backend_Secret extends Mage_Core_Model_Config_Data
{

    protected function _beforeSave()
    {
        parent::_beforeSave();
        $filedKey = end(explode("/", $this->getPath()));

        if ($this->shouldRun()) {
            
            if ($this->getFieldsetDataValue('encryptKeys')) {
                $encryptor = Mage::getModel("core/Encryption");
                $val       = $this->getValue();
                $encVal    = $encryptor->encrypt($val);
                $first2    = substr($val, 0, 2);
                $last2     = substr($val, -2);
                $len       = strlen($val) - 4;
                $mask      = $first2;
                for ($i = 0; $i <= $len; $i++) {
                    $mask = $mask . "#";
                }
                $mask = $mask . $last2;
                Mage::getConfig()->saveConfig("gigya_global/" . $filedKey . "_masked", $mask);
                $this->setValue($encVal);
            }
        }
    }

    protected function shouldRun()
    {
        if ("******" == $this->getValue()) {
            return false;
        } else {
            $inDb = Mage::getStoreConfig($this->getPath());
            $encryptor = Mage::getModel("core/Encryption");
            $dec = $encryptor->decrypt($inDb);
            return $this->getValue() != $dec;
        }
    }

}