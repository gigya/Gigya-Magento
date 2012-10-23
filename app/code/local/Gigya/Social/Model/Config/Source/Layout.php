<?php
class Gigya_Social_Model_Config_Source_layout
{
  /**
   * Options getter
   *
   * @return array
   */
  public function toOptionArray()
  {
    return array(
      array('value' => 'horizontal', 'label'=>Mage::helper('adminhtml')->__('Horizontal')),
      array('value' => 'vertical', 'label'=>Mage::helper('adminhtml')->__('Vertical')),
    );
  }
}
