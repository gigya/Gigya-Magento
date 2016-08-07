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

    public function __construct($confArray)
    {
        $this->mappingConf = $confArray;
    }

    protected function buildKeyedArrays($array)
    {
        $mageKeyedArray = array();
        $gigyaKeyedArray = array();
        foreach ($array as $confItem) {
            $mageKey = $confItem['magentoName'];
            $gigyaKey = $confItem['gigyaName'];
            $direction = empty($confItem['direction']) ? "g2cms" : $confItem['direction'];
            $conf = new Gigya_Social_Helper_FieldMapping_ConfItem($confItem);
            switch ($direction) {
                case "g2cms" :
                    $gigyaKeyedArray[$gigyaKey][] = $conf;
                    break;
                case "cms2g":
                    $mageKeyedArray[$mageKey][] = $conf;
                    break;
                default:
                    $gigyaKeyedArray[$gigyaKey][] = $conf;
                    $mageKeyedArray[$mageKey][] = $conf;
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