<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 6/2/16
 * Time: 8:01 PM
 */
class TestApiKeyValidation extends PHPUnit_Framework_TestCase
{
    const DATA = '{"field":"apikey","groups":{"gigya_global_conf":{"fields":{"enable":{"value":"1"},"apikey":{"value":"3_b2DQkw3a_SBnu0FD6d5b9v3cawzMjH1"},"encryptKeys":{"value":"1"},"useUserKey":{"value":"1"},"secretkey":{"value":""},"userKey":{"value":"AKL1jqD6uYz7"},"userSecret":{"value":"******"},"providers":{"value":"*"},"dataCenter":{"value":"us1.gigya.com"},"laguages":{"value":"auto"},"fallback_lang":{"value":"en"},"loginBehavior":{"value":"loginExistingUser"},"login_retries":{"value":"2"},"google_sa":{"value":"0"},"counters":{"value":"0"},"debug_log":{"value":"0"},"advancedConfig":{"value":""}}}},"group_id":"gigya_global_conf","store_code":"","website_code":"","scope":"default","scope_id":0,"field_config":{"@attributes":{"translate":"label"},"label":"Gigya API key","frontend_type":"text","backend_model":"Gigya_Social_Model_Config_Backend_ApiKey","comment":"Specify the Gigya API key for this domain","sort_order":"9","show_in_default":"1","show_in_website":"1","show_in_store":"1"},"fieldset_data":{"enable":"1","apikey":"3_b2DQkw3a_SBnu0FD6d5b9v3cawzMjH1fhNpw6vctcukk6ZBuxPxj5W3nlFdBOdIN","encryptKeys":"1","useUserKey":"1","secretkey":"","userKey":"AKL1jqD6uYz7","userSecret":"******","providers":"*","dataCenter":"us1.gigya.com","laguages":"auto","fallback_lang":"en","loginBehavior":"loginExistingUser","login_retries":"2","google_sa":"0","counters":"0","debug_log":"0","advancedConfig":""},"path":"gigya_global\/gigya_global_conf\/apikey","value":"3_b2DQkw3a_SBnu0FD6d5b9v3cawzMjH1fhNpw6vctcukk6ZBuxPxj5W3nlFdBOdIN","config_id":"1523"}';
    private $data;

    public function testMissingUserKey()
    {
        $this->setExpectedException(Mage_Core_Exception::class, "Gigya user key is required.");
        $newData = $this->data;
        $newData['fieldset_data']['userKey'] = "";
        $mock = $this->buildMock($newData);
        $beforeSave = new ReflectionMethod("Gigya_Social_Model_Config_Backend_ApiKey", "_beforeSave");
        $beforeSave->setAccessible(true);
        $beforeSave->invoke($mock);
    }

    public function testMissingApiKey()
    {
        $this->setExpectedException(Mage_Core_Exception::class, "Gigya apiKey is required");
        $newData = $this->data;
        $newData['value'] = "";
        $newData['fieldset_data']['apikey'] = "";
        $newData['fieldset_data']['userSecret'] = "secret";
        $mock = $this->buildMock($newData);
        $beforeSave = new ReflectionMethod("Gigya_Social_Model_Config_Backend_ApiKey", "_beforeSave");
        $beforeSave->setAccessible(true);
        $beforeSave->invoke($mock);
    }

    /**
     * This method is called before a test is executed.
     *
     */
    protected function setUp()
    {
        /*        $req = new Mage_Core_Controller_Request_Http();
                $res = new Mage_Core_Controller_Response_Http();
                $controller = new Mage_Adminhtml_System_ConfigController($req, $res);*/
        $this->data = json_decode(self::DATA, true);
    }





    protected function buildMock($dataArray)
    {
        $apiKeyValidator = $this->getMockBuilder("Gigya_Social_Model_Config_Backend_ApiKey")
            ->setMethods(array("getData", "getValue"))->getMock();
        $apiKeyValidator->method("getData")
            ->willReturn($dataArray);
        $apiKeyValidator->method("getValue")
            ->willReturn($dataArray['value']);
        $apiKeyValidator->setData($dataArray);
        return $apiKeyValidator;
    }

}
