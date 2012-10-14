<?php
class Gigya_Social_Module_Config_Source_Loginb
{
  /**
   * Options getter
   *
   * @return array
   */
  public function toOptionArray()
  {
    return array(
      array('value' => 'loginExistingUser', 'label'=>Mage::helper('adminhtml')->__('Login existing user')),
      array('value' => 'alwaysLogin', 'label'=>Mage::helper('adminhtml')->__('Always login')),
    );
  }
}
