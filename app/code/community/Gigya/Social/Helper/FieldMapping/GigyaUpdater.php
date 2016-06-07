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
        $gigyaArray = $this->createGigyaArray();
        $this->callSetAccountInfo($gigyaArray);
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
            $confs = $this->magMappings[$key];
            foreach ($confs as $conf) {
                $transFunc = $conf->getTransFunc();
                if (null != $transFunc) {
                    $value = Gigya_Social_Helper_FieldMapping_Transformers::transformValue($value, $transFunc, $conf,
                        "cms2g");
                }
                $value       = $this->castVal($value, $conf);
                if (null != $value) {
                    $this->assignArrayByPath($gigyaArray, $conf->getGigyaName(), $value);
                }
            }
        }

        return $gigyaArray;
    }

    protected function callSetAccountInfo($gigyaArray)
    {
        $helper = Mage::helper('Gigya_Social');
        $helper->updateGigyaUser($gigyaArray, $this->gigyaUid);
    }

    /**
     * @param mixed                                     $val
     * @param string                                    $transFunc
     * @param Gigya_Social_Helper_FieldMapping_ConfItem $conf
     *
     * @return mixed $val
     */
    private function transformValue($val, $transFunc, $conf)
    {
        if ( ! empty($transFunc)) {
            $callable = array('Gigya_Social_Helper_FieldMapping_Transformers', $transFunc);
            if (is_callable($callable)) {
                $val = call_user_func($callable, "cms2g", $val, null, $conf);
            }
        }

        return $val;
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