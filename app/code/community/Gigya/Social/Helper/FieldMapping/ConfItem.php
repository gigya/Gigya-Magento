<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 5/26/16
 * Time: 10:11 AM
 */
class Gigya_Social_Helper_FieldMapping_ConfItem
{

    /**
     * @var string
     */
    protected $magentoName;
    /**
     * @var string
     */
    protected $magentoType;
    /**
     * @var string
     */
    protected $gigyaName;
    /**
     * @var string
     */
    protected $gigyaType;
    /**
     * @var string
     */
    protected $transFunc = null;

    protected $direction = "both";

    /**
     * Gigya_Social_Helper_FieldMapping_ConfItem constructor.
     */
    public function __construct($array)
    {
        foreach ($array as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return string
     */
    public function getMagentoName()
    {
        return $this->magentoName;
    }

    /**
     * @param string $magentoName
     */
    public function setMagentoName($magentoName)
    {
        $this->magentoName = $magentoName;
    }

    /**
     * @return string
     */
    public function getMagentoType()
    {
        return $this->magentoType;
    }

    /**
     * @param string $magentoType
     */
    public function setMagentoType($magentoType)
    {
        $this->magentoType = $magentoType;
    }

    /**
     * @return string
     */
    public function getGigyaName()
    {
        return $this->gigyaName;
    }

    /**
     * @param string $gigyaName
     */
    public function setGigyaName($gigyaName)
    {
        $this->gigyaName = $gigyaName;
    }

    /**
     * @return string
     */
    public function getGigyaType()
    {
        return $this->gigyaType;
    }

    /**
     * @param string $gigyaType
     */
    public function setGigyaType($gigyaType)
    {
        $this->gigyaType = $gigyaType;
    }

    /**
     * @return string
     */
    public function getTransFunc()
    {
        return $this->transFunc;
    }

    /**
     * @param string $transFunc
     */
    public function setTransFunc($transFunc)
    {
        $this->transFunc = $transFunc;
    }
    
    



}