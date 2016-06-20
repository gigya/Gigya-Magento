<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 5/22/16
 * Time: 12:49 PM
 */
class Gigya_Social_Model_Config_Backend_Secret extends Mage_Core_Model_Config_Data
{

    private $dataChanged = false;
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $filedKey = end(explode("/", $this->getPath()));

        if ($this->shouldRun()) {
            $this->dataChanged = true;
            if ($this->getFieldsetDataValue('encryptKeys')) {
                $encryptor = Mage::getModel("core/Encryption");
                $val       = trim($this->getValue());
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
                $keyName = $filedKey == "secretkey" ? "Gigya site secret" : "Gigya application secret";
                $adminUser = Mage::getSingleton('admin/session')->getUser()->getEmail();
                Mage::log($keyName . " was updated by " . $adminUser, Zend_Log::INFO);
            }
        }

    }

    /**
     * Check if config data value was changed
     *
     * @return bool
     */
    public function isValueChanged()
    {
        return $this->dataChanged;
    }

    protected function shouldRun()
    {
        if ("******" == $this->getValue()) {
            $this->_dataSaveAllowed = false;
            return false;
        } else {
            $inDb = Mage::getStoreConfig($this->getPath());
            $encryptor = Mage::getModel("core/Encryption");
            $dec = $encryptor->decrypt($inDb);
            $changed = $this->getValue() != $dec;
            if ($changed) {
                return true;
            }
            $this->_dataSaveAllowed = false;
            return false;
        }
    }

}