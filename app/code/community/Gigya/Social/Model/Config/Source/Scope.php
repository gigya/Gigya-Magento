<?php
class Gigya_Social_Model_Config_Source_scope
{
  /**
   * Options getter
   *
   * @return array
   */
  public function toOptionArray()
  {
    return array(
      array('value' => 'external', 'label'=>Mage::helper('adminhtml')->__('External')),
      array('value' => 'both', 'label'=>Mage::helper('adminhtml')->__('Both')),
    );
  }
}
