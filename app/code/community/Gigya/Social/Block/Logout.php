<?php
/**
 * Class Gigya_Social_Block_logout
 * @author  Yaniv Aran-Shamir
 */
class Gigya_Social_Block_logout extends Mage_Core_Block_Text_Tag_Js
{
  protected function _toHtml()
  {
    if (Mage::helper('Gigya_Social')->isPluginEnabled('gigya_login/gigya_login_conf')) {
        $js = 'var gigyaSettings = gigyaSettings || {}; gigyaSettings.logout = true;';
        Mage::log($js);
        $this->setContents($js);
        return  parent::_toHtml();
    }
  }
}


