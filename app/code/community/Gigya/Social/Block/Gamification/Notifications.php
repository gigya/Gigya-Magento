<?php
/**
 * Class Gigya_Social_Block_Gamification_notifications
 * @author  Yaniv Aran-Shamir
 */
class Gigya_Social_Block_Gamification_notifications extends Mage_Core_Block_Text_Tag_Js
{
  protected function _toHtml()
  {
    if (Mage::helper('Gigya_Social')->isPluginEnabled('gigya_gamification/gigya_gamification_conf')) {
      if (Mage::getStoreConfig('gigya_gamification/gigya_gamification_conf/notifications')) {
        $js = 'var gigyaSettings = gigyaSettings || {}; gigyaSettings.gm = gigyaSettings.gm || {}; gigyaSettings.gm.notifications = true;';
        $this->setContents($js);
        return  parent::_toHtml();
      }
    }
  }
}


