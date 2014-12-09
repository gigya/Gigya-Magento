<?php
/**
 *  Render the followbar.phtml template file
 * 
 */
class Gigya_Social_Block_Followbar extends Mage_Core_Block_Template
{

  protected function _toHtml()
  {
    if (Mage::helper('Gigya_Social')->isPluginEnabled('gigya_followbar/gigya_followbar_conf')) {
      return  parent::_toHtml();
    }
  }


}