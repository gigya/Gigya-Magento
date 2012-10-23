<?php
class Gigya_Social_Model_Config_Source_counts
{
  /**
   * Options getter
   *
   * @return array
   */
  public function toOptionArray()
  {
    return array(
      array('value' => 'right', 'label'=>Mage::helper('adminhtml')->__('Right')),
      array('value' => 'left', 'label'=>Mage::helper('adminhtml')->__('Left')),
      array('value' => 'top', 'label'=>Mage::helper('adminhtml')->__('Top')),
      array('value' => 'none', 'label'=>Mage::helper('adminhtml')->__('None')),
    );
  }
}
