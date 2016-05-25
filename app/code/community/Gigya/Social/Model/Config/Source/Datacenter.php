<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 7/31/13
 * Time: 3:32 PM
 */
class Gigya_Social_Model_Config_Source_Datacenter
{

    public function toOptionArray()
    {
        return array(
            array('value' => 'us1.gigya.com', 'label' => 'US Data Center'),
            array('value' => 'eu1.gigya.com', 'label' => 'EU Data Center'),
            array('value' => 'au1.gigya.com', 'label' => 'AU Data Center'),
            array('value' => 'ru1.gigya.com', 'label' => 'RU Data Center'),
            array('value' => '', 'label' => 'Other')
        );
    }
}