<?php

include_once __DIR__ . '/../sdk/GSSDK.php';
class Gigya_Social_Helper_Data extends Mage_Core_Helper_Abstract
{
  public function _getPassword($length = 8)
  {
    $characters = str_split('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
    $str = '';
    for ($p = 0; $p < $length; $p++) {
      $str .= $characters[mt_rand(0, count($characters))];
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

  public function notifyLogin($siteUid, $newUser = 'false', $userInfo = array())
  {
    $params = array(
      'siteUID' => $siteUid,
      'newUser' => $newUser,
    );
    if (!empty($userInfo)) {
      $params['userInfo'] = Mage::helper('core')->jsonEncode($userInfo);
    }
    try {
      $res = $this->_gigya_api('notifyLogin', $params);
      if (is_object($res) && $res->getErrorCode() === 0) {
        setcookie($res->getString("cookieName"), $res->getString("cookieValue"), 0, $res->getString("cookiePath"), $res->getString("cookieDomain"));
      }
      else {
        Mage::logException($res);
      }
    }
    catch (Exception $e) {
      $code = $e->getCode();
      $message = $e->getMessage();
      Mage::logException($e);
    }
  }

  public function notifyLogout($siteUid)
  {
    $params = array(
      'siteUID' => $siteUid,
    );
    try {
      $this->_gigya_api('logout', $params);
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
    $data_center = Mage::getStoreConfig('gigya_global/gigya_global_conf/dataCenter');
    $data_center = !empty($data_center) ? $data_center : NULL;
    $apiKey = Mage::getStoreConfig('gigya_global/gigya_global_conf/apikey');
    $secretkey = Mage::getStoreConfig('gigya_global/gigya_global_conf/secretkey');
    $request = new GSRequest($apiKey, $secretkey, 'socialize.' . $method);
    if ($data_center !== NULL){
      $request->setAPIDomain($data_center);
    }
    $params['format'] = 'json';
    foreach ($params as $param => $val) {
      $request->setParam($param, $val);
    }
    try {
      $response = $request->send();
      // If wrong data center resend to right one
      if ($response->getErrorCode() == 301001){
        $data = $response->getData();
        $domain = $data->getString('apiDomain', NULL);
        if ($domain !== NULL){
          Mage::getModel('core/config')->saveConfig('gigya_global/gigya_global_conf/dataCenter', $domain);
          $this->_gigya_api($method, $params);
        } else {
          $ex = new Exception("Bad apiDomain return");
          throw $ex;
        }
      } elseif ($response->getErrorCode() !== 0){
        $exp = new Exception($response->getErrorMessage(), $response->getErrorCode());
        throw $exp;
      }
    }
    catch (Exception $e) {
      $code = $e->getCode();
      $message = $e->getMessage();
      Mage::log($message);
      return $code;
    }

    return $response;
  }

  public function getPluginConfig($pluginName, $format = 'json', $feed = FALSE)
  {
    $config = Mage::getStoreConfig($pluginName);
    foreach ($config as $key =>  $value){
      //fix the magento yes/no as 1 or 0 so it would work in as true/false in javascript
      if ($value === '0' || $value === '1') {
        $config[$key] = ($value) ? true : false;
      }
    }
    if (!empty($config['advancedConfig'])) {
      $advConfig = $this->_confStringToArry($config['advancedConfig']);
      $config = $config + $advConfig;
    }
    unset($config['advancedConfig']);
    if ($feed === TRUE) {
      $config['privacy'] = Mage::getStoreConfig('gigya_activityfeed/gigya_activityfeed_conf/privacy');
    }
    Mage::log($pluginName);
    if ($pluginName == 'gigya_login/gigya_login_conf') {
      $config['baseUrl'] = Mage::getBaseUrl();
    }
    if ($format === 'php') {
      return $config;
    }
    return Mage::helper('core')->jsonEncode($config);
  }

  public function getPluginContainerID($pluginName)
  {
    return Mage::getStoreConfig($pluginName . '/containerID');
  }

  public function isPluginEnabled($pluginName)
  {
    return Mage::getStoreConfig($pluginName . '/enable');
  }
  public function isShareBarEnabled($place)
  {
    return Mage::getStoreConfig('gigya_share/gigya_sharebar/enable_' . $place);
  }
  public function isShareActionEnabled($place)
  {
    return Mage::getStoreConfig('gigya_share/gigya_share_action/enable_' . $place);
  }

  public function _confStringToArry($str)
  {
    $lines = array();
    $values =  explode(PHP_EOL, $str);
    //some clean up
    $values = array_map('trim', $values);
    $values = array_filter($values, 'strlen');
    foreach ($values as  $value) {
      preg_match('/(.*)\|(.*)/', $value, $matches);
      $lines[$matches[1]] = $matches[2];
    }
    return $lines;

  }
}
