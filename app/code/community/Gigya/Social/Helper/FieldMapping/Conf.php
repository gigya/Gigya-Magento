<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 5/26/16
 * Time: 9:06 AM
 */
class Gigya_Social_Helper_FieldMapping_Conf
{

    private $magentoKeyed;
    private $gigyaKeyed;
    private $mappingConf;

    public function __construct($json)
    {
        $this->mappingConf = json_decode($json, true);
    }

    protected function buildKeyedArrays($array)
    {
        $mageKeyedArray = array();
        $gigyaKeyedArray = array();
        foreach ($array as $confItem) {
            $mageKey = $confItem['magentoName'];
            $gigyaKey = $confItem['gigyaName'];
            $direction = $confItem['direction'];
            switch ($direction) {
                case "g2cms" :
                    $gigyaKeyedArray[$gigyaKey][] = new Gigya_Social_Helper_FieldMapping_ConfItem($confItem);
                    break;
                case "cms2g":
                    $mageKeyedArray[$mageKey][] = new Gigya_Social_Helper_FieldMapping_ConfItem($confItem);
                    break;
                default:
                    $gigyaKeyedArray[$gigyaKey][] = new Gigya_Social_Helper_FieldMapping_ConfItem($confItem);
                    $mageKeyedArray[$mageKey][] = new Gigya_Social_Helper_FieldMapping_ConfItem($confItem);
                    break;
            }
        }
        $this->gigyaKeyed = $gigyaKeyedArray;
        $this->magentoKeyed = $mageKeyedArray;
    }


    /**
     * @return array
     */
    public function getMagentoKeyed()
    {
        if (empty($this->magentoKeyed)) {
            $this->buildKeyedArrays($this->mappingConf);
        }
        return $this->magentoKeyed;
    }

    /**
     * @return array
     */
    public function getGigyaKeyed()
    {
        if (empty($this->gigyaKeyed)) {
            $this->buildKeyedArrays($this->mappingConf);
        }
        return $this->gigyaKeyed;
    }

    /**
     * @return array
     */
    public function getMappingConf()
    {
        return $this->mappingConf;
    }



}