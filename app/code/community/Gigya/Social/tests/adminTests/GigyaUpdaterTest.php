<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 6/7/16
 * Time: 1:14 PM
 */
class GigyaUpdaterTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Mage_Customer_Model_Customer
     */
    private $magentoAccount;

    public function testGigyaAccountUpdate()
    {
        $expectedArray = array(
            "data" => array("test" => "test string"),
            "profile" => array("gender" => "f")
        );
        $this->magentoAccount->setData("gender", 2);
        $this->magentoAccount->setData("test1", "test string");
        $gigyaUpdater = $this->getMockBuilder(Gigya_Social_Helper_FieldMapping_GigyaUpdater::class)
            ->setMethods(["callSetAccountInfo"])
            ->setConstructorArgs([$this->magentoAccount->getData(), $this->magentoAccount->getData("gigya_uid")])
            ->getMock();

        $gigyaUpdater->setPath(__DIR__ . DS . ".." . DS . "resources" . DS . "mappings.json");
        $gigyaUpdater->expects($this->once())->method("callSetAccountInfo")->with($expectedArray);
        $gigyaUpdater->updateGigya();
    }

    public function testDeepValue()
    {
        $expectedArray = array(
            "data" => array("deep" => array("deep" => array("deep" => array("very" => array("very" => array("deep" => array("value" => "test string"))))))),
        );
        $this->magentoAccount->setData("deep", "test string");
        $gigyaUpdater = $this->getMockBuilder(Gigya_Social_Helper_FieldMapping_GigyaUpdater::class)
            ->setMethods(["callSetAccountInfo"])
            ->setConstructorArgs([$this->magentoAccount->getData(), $this->magentoAccount->getData("gigya_uid")])
            ->getMock();

        $gigyaUpdater->setPath(__DIR__ . DS . ".." . DS . "resources" . DS . "mappings_deep.json");
        $gigyaUpdater->expects($this->once())->method("callSetAccountInfo")->with($expectedArray);
        $gigyaUpdater->updateGigya();
    }

    public function testCasting()
    {
        $string = "1";
        $int = 1;
        $long = 10000000000;
        $bool = true;
        
        $expectedArray = array(
          "data" => array("string" => $string, "int" => $int, "long" => $long, "bool" => $bool )
        );
        $this->magentoAccount->setData("test_string", $int);
        $this->magentoAccount->setData("test_int", $string);
        $this->magentoAccount->setData("test_long", "10000000000");
        $this->magentoAccount->setData("test_bool", "TRUE");
        $gigyaUpdater = $this->getMockBuilder(Gigya_Social_Helper_FieldMapping_GigyaUpdater::class)
            ->setMethods(["callSetAccountInfo"])
            ->setConstructorArgs([$this->magentoAccount->getData(), $this->magentoAccount->getData("gigya_uid")])
            ->getMock();

        $gigyaUpdater->setPath(__DIR__ . DS . ".." . DS . "resources" . DS . "mappings_casting_to_gigya.json");
        $gigyaUpdater->expects($this->once())->method("callSetAccountInfo")->with($expectedArray);
        $gigyaUpdater->updateGigya();

        

    }

    protected function setUp()
    {
        $this->magentoAccount = new Mage_Customer_Model_Customer();
        $this->magentoAccount->setData("gigya_uid", "test_gigya_uid");
    }

}
