<?php
class Gigya_Social_Model_Config_Source_countType
{
  /**
   * Options getter
   *
   * @return array
   */
  public function toOptionArray()
  {
    return array(
      array('value' => 'number', 'label'=>Mage::helper('adminhtml')->__('Number')),
      array('value' => 'percentage', 'label'=>Mage::helper('adminhtml')->__('Percentage')),
    );
  }
}
