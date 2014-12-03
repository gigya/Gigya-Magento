<?php
/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 6/19/14
 * Time: 4:04 PM
 */

class Gigya_Social_Model_Config_Backend_ApiKey extends Mage_Core_Model_Config_Data {
    protected function _beforeSave()
    {
        $value = $this->getValue();
        $helper = Mage::helper('Gigya_Social');
        $helper->utils->setApiKey($value);
        $data = $this->getData();
        $secret = $data['fieldset_data']['secretkey'];
        $userKey = $data['fieldset_data']['userKey'];
        $userSecret = $data['fieldset_data']['userSecret'];
        $useUserKey = $data['fieldset_data']['useUserKey'];
	    $dataCenter = $this->_setDataCenter($data['fieldset_data']);

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
		    Mage::throwException(Mage::helper('adminhtml')->__("Gigya data center not selected. when chosing 'other' you should fill in the Data center code provided by Gigya manually."));
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
	protected function _setDataCenter($fieldset)
	{
		$dataCenter = '';

		if ($fieldset['dataCenter']) {
			$dataCenter = $fieldset['dataCenter'];
		} elseif ($fieldset['dataCenterOther']) {
			$dataCenter = $fieldset['dataCenterOther'] . '.gigya.com';
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