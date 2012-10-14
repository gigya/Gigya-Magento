<?php
class Gigya_Social_Module_Config_Source_buttons
{
  /**
   * Options getter
   *
   * @return array
   */
  public function toOptionArray()
  {
    return array(
      array('value' => 'standart', 'label'=>Mage::helper('adminhtml')->__('Icons')),
      array('value' => 'fullLogo', 'label'=>Mage::helper('adminhtml')->__('Full logos')),
    );
  }
}
