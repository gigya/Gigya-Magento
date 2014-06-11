<?php
class Gigya_Social_Model_Config_Source_loginModes
{
  /**
   * Options getter
   *
   * @return array
   */
  public function toOptionArray()
  {
    return array(
      array('value' => 'disable', 'label'=>Mage::helper('adminhtml')->__('Magento Only')),
      array('value' => 'social', 'label'=>Mage::helper('adminhtml')->__('Magento + Social Login')),
			array('value' => 'raas', 'label'=>Mage::helper('adminhtml')->__('Registration-as-a-Service')),
    );
  }
}
