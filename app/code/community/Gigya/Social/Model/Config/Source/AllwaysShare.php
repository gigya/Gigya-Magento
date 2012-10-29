
<?php
class Gigya_Social_Model_Config_Source_allwaysShare
{
  /**
   * Options getter
   *
   * @return array
   */
  public function toOptionArray()
  {
    return array(
      array('value' => 'hide', 'label'=>Mage::helper('adminhtml')->__('do not display the "Always share" checkbox')),
      array('value' => 'checked', 'label'=>Mage::helper('adminhtml')->__('the "Always share" checkbox is displayed and checked by default')),
      array('value' => 'unchecked', 'label'=>Mage::helper('adminhtml')->__('the "Always share" checkbox is displayed and unchecked by default')),
    );
  }
}
