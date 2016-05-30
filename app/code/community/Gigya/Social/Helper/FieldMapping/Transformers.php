<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 5/26/16
 * Time: 12:00 PM
 */
class Gigya_Social_Helper_FieldMapping_Transformers
{



    public function genderConvert($direction, $cmsVal, $gigyaVal, $conf = array())
    {
        $mapping = array(
            "m" => 1,
            "f" => 2,
            "u" => 0
        );
        if ("g2cms" == $direction) {
            return $mapping[$gigyaVal];
        }
        if ("cms2g" == $direction) {
            $fliped = array_flip($mapping);
            return $fliped[$cmsVal];
        }
        return null;
    }


    /**
     * @param mixed $val
     * @param string $transFunc
     * @param Gigya_Social_Helper_FieldMapping_ConfItem $conf
     *
     * @return mixed $val
     */
    public static function transformValue($val, $transFunc, $conf, $direction)
    {
        if (!empty($transFunc)) {
            $callable = array(__CLASS__, $transFunc);
            if (is_callable($callable)) {
                $val = call_user_func($callable, $direction, $val, null, $conf);
            }
        }
        return $val;
    }
}