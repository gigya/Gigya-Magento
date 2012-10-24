<?php
class Gigya_Social_Block_Login extends Mage_Core_Block_Template
{
  protected function _prepareLayout(){
    return parent::_prepareLayout();

  }
  protected function _getLoginConfig() {
    $conf = array();
    $conf['containerID'] = Mage::getStoreConfig('gigya_login/gigya_login_conf/loginContainerId');
    $conf['buttonsStyle'] = Mage::getStoreConfig('gigya_login/gigya_login_conf/buttonStyle');
    $conf['showTermsLink'] = Mage::getStoreConfig('gigya_login/gigya_login_conf/showTerms');
    $conf['width'] = Mage::getStoreConfig('gigya_login/gigya_login_conf/loginWidth');
    $conf['height'] = Mage::getStoreConfig('gigya_login/gigya_login_conf/loginHeight');
    return json_encode($conf);
  }
  protected function _getContainerId() {
    return Mage::getStoreConfig('gigya_login/gigya_login_conf/loginContainerId');
  }
  protected function _toHtml(){
    return parent::_toHtml();
  }

}
