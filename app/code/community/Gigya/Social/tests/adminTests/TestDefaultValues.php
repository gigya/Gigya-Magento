<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 6/1/16
 * Time: 5:24 PM
 */
class TestDefaultValues extends PHPUnit_Framework_TestCase
{


    public function testDefaultValue()
    {
        echo "Testing default configuration values" . PHP_EOL;
        echo "Checking enabled by default" . PHP_EOL;
        $this->assertEquals(1, reset(Mage::getConfig()->getNode("default/gigya_global/gigya_global_conf/enable")));
        echo "checking default data center" . PHP_EOL;
        $this->assertEquals("us1.gigya.com", reset(Mage::getConfig()->getNode("default/gigya_global/gigya_global_conf/dataCenter")));
    }

}
