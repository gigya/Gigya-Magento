<?php
class Gigya_Social_Model_Config_Source_leaderboard
{
  /**
   * Options getter
   *
   * @return array
   */
  public function toOptionArray()
  {
    return array(
      array('value' => 'all', 'label'=>Mage::helper('adminhtml')->__('All')),
      array('value' => '7days', 'label'=>Mage::helper('adminhtml')->__('7 Days')),
    );
  }
}
