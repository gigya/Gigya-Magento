<?php
/**
 * Backend module for API key validation
 * When API key is submitted, validate with Gigya the api key by running a test method (socialize.shortenURL)
 * All required global parameters are tested with the API test
 */

class Gigya_Social_Model_Config_Backend_ApiKey extends Mage_Core_Model_Config_Data {
  /*
   * Retrieve the submitted api data and validate it with Gigya
   * If a field was not submitted, take it from default config
   */
    protected function _beforeSave()
    {
	  $default_config = Mage::getStoreConfig('gigya_global/gigya_global_conf'); // default store config
	  $value = $this->getValue(); // the newly submitted values in _data
	  $helper = Mage::helper('Gigya_Social');
	  $helper->utils->setApiKey($value); // set the api key to the submitted key
	  $data = $this->getData();
	  // test if other fields were submitted, or use default values
	  // secret
	  if($data['fieldset_data']['secretkey']) {
		$secret = $data['fieldset_data']['secretkey'];
	  } else {
		$secret = $default_config['secretkey'];
	  }
	  // userkey
	  if($data['fieldset_data']['userKey'])  {
		$userKey = $data['fieldset_data']['userKey'];
	  } else {
		$userKey = $default_config['userKey'];
	  }
	  // userSecret
	  if($data['fieldset_data']['userSecret']) {
		$userSecret = $data['fieldset_data']['userSecret'];
	  } else {
		$userSecret = $default_config['userSecret'];
	  }
	  $useUserKey = $data['fieldset_data']['useUserKey'] ? $data['fieldset_data']['useUserKey'] : $default_config['useUserKey'];

	  $dataCenter = $this->_setDataCenter($data['fieldset_data'], $default_config['dataCenter']);

	  if ($useUserKey) {
		  if(empty($useUserKey)){
			  Mage::throwException(Mage::helper('adminhtml')->__("Gigya user key is required."));
		  }
		  if (empty($userSecret)){
			  Mage::throwException(Mage::helper('adminhtml')->__("Gigya user secret is required."));

		  }
		  $helper->utils->setUserKey($userKey);
		  $helper->utils->setUserSecret($userSecret);
		  $helper->utils->setUseUserKey($useUserKey);
	  } else {
		  $helper->utils->setApiSecret($secret);
	  }

	  // validate that data center is chosen/filled before setting
	  if (empty($dataCenter)) {
		  Mage::throwException(Mage::helper('adminhtml')->__("Gigya data center not selected. when choosing 'other' you should fill in the Data center code provided by Gigya manually."));
	  }

	  $helper->utils->setApiDomain($dataCenter);

	  if ($apiError = $helper->utils->testApiconfig()) {
		  $_connectError = $this->_APIerrorHandler($apiError, $dataCenter);
		  Mage::throwException(Mage::helper('adminhtml')->__($_connectError));
	  }

    }

	/**
	 * Retrieve the selected or manually added data center
	 * @param $fieldset
	 * @return string
	 */
	protected function _setDataCenter($fieldset, $default_dc)
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
	 * @param $error
     * @param $dataCenter
     *
	 * @return string $errMsg
	 */
	protected  function _APIerrorHandler($error, $dataCenter) {
		switch ($error) {
			case '301001' :
				$errMsg = "The data center region you have configured: " . $dataCenter . ", does not correspond with the site data center set in Gigya console for this API.";
				break;
			case '400093' :
				$errMsg = "Invalid ApiKey parameter.";
			default :
				$errMsg = "Something went wrong, please try again or contact Gigya for more details. error code:" . $error
                          . ".<a href='http://developers.gigya.com/037_API_reference/zz_Response_Codes_and_Errors' target='_blank'> Response Codes and Errors</a>" ;
		}
		return $errMsg;
	}


} 