<?php
class Gigya_Social_Model_Config_Source_privacy
{
  /**
   * Options getter
   *
   * @return array
   */
  public function toOptionArray()
  {
    return array(
      array('value' => 'private', 'label'=>Mage::helper('adminhtml')->__('Private')),
      array('value' => 'friends', 'label'=>Mage::helper('adminhtml')->__('Friends')),
      array('value' => 'public', 'label'=>Mage::helper('adminhtml')->__('Public')),
    );
  }
}
