<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 6/6/16
 * Time: 1:51 PM
 */
class MagentoUpdaterTest extends PHPUnit_Framework_TestCase
{

    private $gigyaAccount;
    /**
     * @var Mage_Customer_Model_Customer
     */
    private $magentoAccount;

    public function testUpdateAccount()
    {
        $updater = new Gigya_Social_Helper_FieldMapping_MagentoUpdater($this->gigyaAccount);
        $updater->setPath(__DIR__ . DS . ".." . DS . "resources" . DS . "mappings.json");
        $updater->updateMagentoAccount($this->magentoAccount);
        $this->assertEquals(1, $this->magentoAccount->getData("gender"));
        $this->assertEquals("test string", $this->magentoAccount->getData("test1"));
    }

    public function testVeryDeepValue()
    {
        $updater = new Gigya_Social_Helper_FieldMapping_MagentoUpdater($this->gigyaAccount);
        $updater->setPath(__DIR__ . DS . ".." . DS . "resources" . DS . "mappings_deep.json");
        $updater->updateMagentoAccount($this->magentoAccount);
        $this->assertEquals("very very deep", $this->magentoAccount->getData("deep"));
    }
    
    public function testArrayValue()
    {
        $json    = '{"emails": ["pj00002he@btinternet.com"],"unverifiedEmails": []}';
        $updater = new Gigya_Social_Helper_FieldMapping_MagentoUpdater($this->gigyaAccount);
        $updater->setPath(__DIR__ . DS . ".." . DS . "resources" . DS . "mappings_array.json");
        $updater->updateMagentoAccount($this->magentoAccount);
        $this->assertJsonStringEqualsJsonString($json, $this->magentoAccount->getData("array_test"));
    }

    public function testValueCasting()
    {
        $updater = new Gigya_Social_Helper_FieldMapping_MagentoUpdater($this->gigyaAccount);
        $updater->setPath(__DIR__ . DS . ".." . DS . "resources" . DS . "mappings_casting.json");
        $updater->updateMagentoAccount($this->magentoAccount);
        $this->assertTrue(is_numeric($this->magentoAccount->getData("test_int")), "Testing integer casting");
        $this->assertTrue(is_string($this->magentoAccount->getData("test_string")), "Testing string casting");
        $this->assertNotFalse(strtotime($this->magentoAccount->getData("test_datetime_epoch")), "Testing datetime epoch casting");
        $this->assertNotFalse(strtotime($this->magentoAccount->getData("test_datetime_string")), "Testing datetime string casting");
        $this->assertTrue(is_float($this->magentoAccount->getData("test_float")), "Testing float casting");
    }

    public function testIsMapped()
    {
        $updater = new Gigya_Social_Helper_FieldMapping_MagentoUpdater($this->gigyaAccount);
        $this->assertTrue($updater->isMapped(), "Testing mapped is set to true");
        Mage::getConfig()->setNode("global/gigya/mapping_file", "");
        $updater = new Gigya_Social_Helper_FieldMapping_MagentoUpdater($this->gigyaAccount);
        $this->assertFalse($updater->isMapped(), "Testing mapped is false");
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    protected function setUp()
    {
        $json                 = file_get_contents(__DIR__ . DS . ".." . DS . "resources" . DS . "account.json");
        $this->gigyaAccount   = json_decode($json, true);
        $this->magentoAccount = $this->getMockBuilder(Mage_Customer_Model_Customer::class)
            ->setMethods(["save"])->getMock();

    }

}
