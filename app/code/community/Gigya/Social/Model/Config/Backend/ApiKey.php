<?php

/**
 * Backend module for API key validation
 * When API key is submitted, validate with Gigya the api key by running a test method (socialize.shortenURL)
 * All required global parameters are tested with the API test
 */
class Gigya_Social_Model_Config_Backend_ApiKey extends Mage_Core_Model_Config_Data
{

    private $beforeChange;
    /*
     * Retrieve the submitted api data and validate it with Gigya
     * If a field was not submitted, take it from default config
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->beforeChange = Mage::getStoreConfig('gigya_global/gigya_global_conf'); // default store config
        $value          = $this->getValue(); // the newly submitted values in _dataa
        if (empty($value)) {
            Mage::throwException(Mage::helper('adminhtml')->__("Gigya apiKey is required"));
        }
        $data           = $this->getData();
        $useUserKey     = $data['fieldset_data']['useUserKey'];
        if ($this->shouldRun($useUserKey, $this->beforeChange)) {
            $helper = Mage::helper('Gigya_Social');
            $helper->utils->setApiKey($value); // set the api key to the submitted key
            // test if other fields were submitted, or use default values
            // secret
            $secret = $this->getSecret("secretkey");
            // userkey
            $userKey = trim($this->getFieldsetDataValue("userKey"));
            // userSecret
            $userSecret = $this->getSecret("userSecret");

            $dataCenter = strtolower(trim($this->_setDataCenter($data['fieldset_data'])));

            $helper->utils->setUseUserKey($useUserKey);
            if ($useUserKey) {
                if (empty($userKey) && empty($this->beforeChange['userKey'])) {
                    Mage::throwException(Mage::helper('adminhtml')->__("Gigya user key is required."));
                }
                if (empty($userSecret) && empty($this->beforeChange['userSecret'])) {
                    Mage::throwException(Mage::helper('adminhtml')->__("Gigya user secret is required."));

                }
                $helper->utils->setUserKey($userKey);
                $helper->utils->setUserSecret($userSecret);
            } else {
                if (empty($secret) && empty($this->beforeChange['secretkey'])) {
                    Mage::throwException(Mage::helper('adminhtml')->__("Gigya secret is required."));
                }
                $helper->utils->setApiSecret($secret);
            }

            // validate that data center is chosen/filled before setting
            if (empty($dataCenter)) {
                Mage::throwException(Mage::helper('adminhtml')->__("Gigya data center not selected. when choosing 'other' you should fill in the Data center code provided by Gigya manually."));
            }

            $helper->utils->setApiDomain($dataCenter);
            $this->testConfig($helper, $dataCenter);

        }

    }

    protected function testConfig($helper, $dataCenter)
    {
        if ($apiError = $helper->utils->testApiconfig()) {
            $_connectError = $this->_APIerrorHandler($apiError, $dataCenter);
            Mage::throwException(Mage::helper('adminhtml')->__($_connectError));
        }

    }

    protected function shouldRun($useUserKey, $current)
    {
        $apiKeyChanged = $this->hasChanged($this->getFieldsetDataValue("apikey"), $current['apikey']);
        $dataCenterChanged = $this->hasChanged($this->getFieldsetDataValue("dataCenter"), $current['dataCenter'] );
        $userKeyChanged = $this->hasChanged($this->getFieldsetDataValue("userKey"), $current['userKey']);
            $apiOrDcOrUserKey = $apiKeyChanged || $dataCenterChanged || $userKeyChanged;
            if ($useUserKey) {
                $secretChanged = $this->getFieldsetDataValue('userSecret') != "******";
            } else {
                $secretChanged = $this->getFieldsetDataValue('secretkey') != "******";
            }

            return $apiOrDcOrUserKey || $secretChanged;

    }

    private function hasChanged($old, $new)
    {
        return $old != $new;
    }

    private function getSecret($key)
    {
        $formVal = $this->getFieldsetDataValue($key);
        if ("******" == $formVal) {
            $encSecret = $this->beforeChange[$key];
            $dec = Mage::helper('core')->decrypt($encSecret);
            return $dec;
        }
        return $this->getFieldsetDataValue($key);
    }

    /**
     * Retrieve the selected or manually added data center
     *
     * @param $fieldset
     *
     * @return string
     */
    protected function _setDataCenter($fieldset, $default_dc = "us1.gigya.com")
    {
        $dataCenter = '';

        if ($fieldset['dataCenter']) {
            $dataCenter = $fieldset['dataCenter'];
        } elseif ($fieldset['dataCenterOther']) {
            $dataCenter = $fieldset['dataCenterOther'] . '.gigya.com';
        } else {
            $dataCenter = $default_dc;
        }

        return $dataCenter;
    }

    /**
     * Provide error code messages
     * http://developers.gigya.com/037_API_reference/zz_Response_Codes_and_Errors
     *
     * @param $error
     * @param $dataCenter
     *
     * @return string $errMsg
     */
    protected function _APIerrorHandler($error, $dataCenter)
    {
        switch ($error) {
            case '301001' :
                $errMsg = "The data center region you have configured: " . $dataCenter . ", does not correspond with the site data center set in Gigya console for this API.";
                break;
            case '400093' :
                $errMsg = "Invalid ApiKey parameter.";
                break;
            default :
                $errMsg = "Something went wrong, please try again or contact Gigya for more details. error code:" . $error
                    . ".<a href='http://developers.gigya.com/037_API_reference/zz_Response_Codes_and_Errors' target='_blank'> Response Codes and Errors</a>";
        }

        return $errMsg;
    }

}