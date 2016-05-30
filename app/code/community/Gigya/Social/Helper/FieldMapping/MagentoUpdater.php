<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 5/29/16
 * Time: 4:47 PM
 */
class Gigya_Social_Helper_FieldMapping_MagentoUpdater
{

    private $gigyaAccount;
    private $gigyaMapping;
    /**
     * @var bool
     */
    private $mapped = false;
    private $path;

    /**
     * Gigya_Social_Helper_FieldMapping_MagentoUpdater constructor.
     *
     * @param array $gigyaAccount
     */
    public function __construct($gigyaAccount)
    {
        $this->gigyaAccount = $gigyaAccount;
        $this->path         = (string) Mage::getConfig()->getNode("global/gigya/mapping_file");
        $this->mapped       = ! empty($this->path);
    }

    /**
     * @param Mage_Customer_Model_Customer $magentoAccount
     *
     * @throws Exception
     */
    public function updateMagentoAccount(&$magentoAccount)
    {
        try {
            $this->retrieveFieldMappings();
            Mage::dispatchEvent("gigya_pre_filed_mapping", array("gigyaAccount" => $this->gigyaAccount));
            $this->setAccountValues($magentoAccount);
            $magentoAccount->save();
        } catch (Exception $e) {
            Mage::log("Error mapping fields from Gigya to Magento. Magento Error" . $e->getMessage());
            Mage::logException($e);
        }
    }

    /**
     * @return boolean
     */
    public function isMapped()
    {
        if (Mage::helper('Gigya_Social')->isDebug()) {
            Mage::log("Field mapping is not enabled", Zend_Log::DEBUG, "gigya_debug_log");
        }
        return $this->mapped;
    }

    protected function retrieveFieldMappings()
    {
        $mappingJson = file_get_contents($this->path);
        if (false === $mappingJson) {
            $err     = error_get_last();
            $message = "Could not retrieve field mapping configuration file. message was:" . $err['message'];
            Mage::log($message, Zend_Log::ERR);
            throw new Exception("$message");
        }
        $conf               = new Gigya_Social_Helper_FieldMapping_Conf($mappingJson);
        $this->gigyaMapping = $conf->getGigyaKeyed();
    }

    /**
     * @param Mage_Customer_Model_Customer $account
     */
    protected function setAccountValues(&$account)
    {
        foreach ($this->gigyaMapping as $gigyaName => $confs) {
            /** @var Gigya_Social_Helper_FieldMapping_ConfItem $conf */
            $value = $this->getValueFromGigyaAccount($gigyaName);
            foreach ($confs as $conf) {
                $mageKey   = $conf->getMagentoName();
                $val       = $this->castValue($value, $conf);
                $transFunc = $conf->getTransFunc();
                if (null != $transFunc) {
                    $val = Gigya_Social_Helper_FieldMapping_Transformers::transformValue($val, $transFunc, $conf,
                        "g2cms");
                }
                $account->setData($mageKey, $val);
            }
        }
    }

    private function getValueFromGigyaAccount($path)
    {
        $accArray = $this->gigyaAccount;
        $keys     = explode(".", $path);
        foreach ($keys as $key) {
            if (isset($accArray[$key])) {
                $accArray = $accArray[$key];
            } else {
                $accArray = null;
            }
        }
        if (is_array($accArray) || is_object($accArray)) {
            $accArray = json_encode($accArray, JSON_UNESCAPED_SLASHES);
        }

        return $accArray;
    }

    /**
     * @param mixed                                     $value
     * @param Gigya_Social_Helper_FieldMapping_ConfItem $conf
     *
     * @return mixed
     */
    private function castValue($value, $conf)
    {
        switch ($conf->getMagentoType()) {
            case "datetime":
                $value = Mage::helper('core')->formatDate($value, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, false);
                break;
            case "decimal":
                $value = (float) $value;
                break;
            case "int":
                $value = (int) $value;
                break;
            case "text":
                $value = (string) $value;
                break;
            case "varchar":
                $value = (string) $value;
                break;
        }

        return $value;
    }
}