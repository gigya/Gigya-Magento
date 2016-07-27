<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 5/26/16
 * Time: 2:05 PM
 */
class Gigya_Social_Helper_FieldMapping_GigyaUpdater
{

    private $magMappings;
    private $cmsArray;
    private $gigyaUid;
    private $mapped;
    private $path;
    private $gigyaArray;

    /**
     * Gigya_Social_Helper_FieldMapping_GigyaUpdater constructor.
     */
    public function __construct($cmsValuesArray, $gigyaUid)
    {
        $this->cmsArray = $cmsValuesArray;
        $this->gigyaUid = $gigyaUid;
        $this->path     = (string) Mage::getConfig()->getNode("global/gigya/mapping_file");
        $this->mapped   = ! empty($this->path);

    }

    public function updateGigya()
    {
        $this->retrieveFieldMappings();
        Mage::dispatchEvent("pre_sync_to_gigya", array("updater" => $this));
        $this->gigyaArray = $this->createGigyaArray();
        $this->callSetAccountInfo();
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

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getCmsArray()
    {
        return $this->cmsArray;
    }

    /**
     * @param mixed $cmsArray
     */
    public function setCmsArray($cmsArray)
    {
        $this->cmsArray = $cmsArray;
    }

    /**
     * @return mixed
     */
    public function getGigyaArray()
    {
        return $this->gigyaArray;
    }

    /**
     * @param mixed $gigyaArray
     */
    public function setGigyaArray($gigyaArray)
    {
        $this->gigyaArray = $gigyaArray;
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
        $conf              = new Gigya_Social_Helper_FieldMapping_Conf($mappingJson);
        $this->magMappings = $conf->getMagentoKeyed();
    }

    protected function createGigyaArray()
    {
        $gigyaArray = array();
        foreach ($this->cmsArray as $key => $value) {
            /** @var Gigya_Social_Helper_FieldMapping_ConfItem $conf */
            if (isset($this->magMappings[$key])) {
              $confs = $this->magMappings[$key];
              foreach ($confs as $conf) {
                $value = $this->castVal($value, $conf);
                if (NULL != $value) {
                  $this->assignArrayByPath(
                    $gigyaArray, $conf->getGigyaName(), $value
                  );
                }
              }
            }
        }

        return $gigyaArray;
    }

    protected function callSetAccountInfo()
    {
        $helper = Mage::helper('Gigya_Social');
        $helper->updateGigyaUser($this->gigyaArray, $this->gigyaUid);
    }


    /**
     * @param mixed                                     $val
     * @param Gigya_Social_Helper_FieldMapping_ConfItem $conf
     *
     * @return mixed $val;
     */

    private function castVal($val, $conf)
    {
        switch ($conf->getGigyaType()) {
            case "string":
                return (string) $val;
                break;
            case "long";
            case "int":
                return (int) $val;
                break;
            case "bool":
                if (is_string($val)) {
                    $val = strtolower($val);
                }
                return filter_var($val, FILTER_VALIDATE_BOOLEAN);
                break;
            default:
                return $val;
                break;
        }
    }

    private function assignArrayByPath(&$arr, $path, $value, $separator = '.')
    {
        $keys = explode($separator, $path);

        foreach ($keys as $key) {
            $arr = &$arr[$key];
        }

        $arr = $value;
    }

}