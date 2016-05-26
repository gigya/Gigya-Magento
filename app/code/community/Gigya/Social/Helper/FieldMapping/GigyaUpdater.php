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
    private $transformer;

    /**
     * Gigya_Social_Helper_FieldMapping_GigyaUpdater constructor.
     */
    public function __construct($cmsValuesArray)
    {
        $this->cmsArray = $cmsValuesArray;

    }

    public function updateGigya()
    {
        $this->retrieveFieldMappings();
        $gigyaArray = $this->createGigyaArray();

    }

    protected function retrieveFieldMappings()
    {
        $path        = (string) Mage::getConfig()->getNode("global/gigya/mapping_file");
        $mappingJson = file_get_contents($path);
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
                $val = $this->castVal($value, $conf);
                $transFunc = $conf->getTransFunc();
                if (null != $transFunc) {
                    $val = $this->transformValue($val,$transFunc, $conf);
                }
                $this->assignArrayByPath($gigyaArray, $conf->getGigyaName(), $val);
            }
        }
        return $gigyaArray;
    }

    /**
     * @param mixed $val
     * @param string $transFunc
     * @param Gigya_Social_Helper_FieldMapping_ConfItem $conf
     *
     * @return mixed $val
     */
    private function transformValue($val, $transFunc, $conf)
    {
        if (!empty($transFunc)) {
            if (method_exists($this->transformer, $transFunc)) {
                $val = $this->transformer->$transFunc("cms2g", $val, null, $conf);
            }
        }
        return $val;
    }

    /**
     * @param mixed $val
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
            case "int":
                return (int) $val;
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