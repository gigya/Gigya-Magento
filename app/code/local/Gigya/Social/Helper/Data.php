<?php

include_once __DIR__ . '/../sdk/GSSDK.php';
class Gigya_Social_Helper_Data extends Mage_Core_Helper_Abstract
{
  public function _getPassword($length = 8)
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str = '';
    for ($p = 0; $p < $length; $p++) {
      $str .= $characters[mt_rand(0, strlen($characters))];
    }
    return 'Gigya_' . $str;
  }

  public function notifyRegistration($gigyaUid, $siteUid)
  {
    $params = array(
      'UID'     => $gigyaUid,
      'siteUID' => $siteUid,
    );
    try {
      $this->_gigya_api('notifyRegistration', $params);
    }
    catch (Exception $e) {
      $code = $e->getCode();
      $message = $e->getMessage();
      Mage::logException($e);
    }
  }
  public function notifyLogin($siteUid)
  {
    $params = array(
      'siteUID' => $siteUid,
    );
    try {
      $this->_gigya_api('notifyLogin', $params);
    }
    catch (Exception $e) {
      $code = $e->getCode();
      $message = $e->getMessage();
      Mage::logException($e);
    }
  }

  public function deleteAccount($gigyaUid)
  {
    $params = array(
      'UID'     => $gigyaUid,
    );
    try {
      $res = $this->_gigya_api('deleteAccount', $params);
      Mage::log($res);

    }
    catch (Exception $e) {
      $code = $e->getCode();
      $message = $e->getMessage();
      Mage::logException($e);
    }
  }


  /**
   * Helper function that handles Gigya API calls.
   *
   * @param mixed $method
   *   The Gigya API method.
   * @param mixed $params
   *   The method parameters.
   *
   * @return array
   *   The Gigya response.
   */
  public function _gigya_api($method, $params) {
    $apiKey = Mage::getStoreConfig('gigya_global/gigya_global_conf/apikey');
    $secretkey = Mage::getStoreConfig('gigya_global/gigya_global_conf/secretkey');
    $request = new GSRequest($apiKey, $secretkey, $method);
    $params['format'] = 'json';
    foreach ($params as $param => $val) {
      $request->setParam($param, $val);
    }
    try {
      $response = $request->send();
    }
    catch (Exception $e) {
      $code = $e->getCode();
      $message = $e->getMessage();
      Mage::log($message);
      return $code;
    }

    return $response;
  }



}
