<?php
class Gigya_Social_Model_Config_Source_imageb
{
  /**
   * Options getter
   *
   * @return array
   */
  public function toOptionArray()
  {
    return array(
      array('value' => 'default', 'label'=>Mage::helper('adminhtml')->__('Use image tag if exists, first image on post otherwise.')),
      array('value' => 'first', 'label'=>Mage::helper('adminhtml')->__('First image on the post')),
      array('value' => 'url', 'label'=>Mage::helper('adminhtml')->__('Specify an image URL')),
    );
  }
}
