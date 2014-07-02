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
    $opts = array(
      array('value' => 'disable', 'label'=>Mage::helper('adminhtml')->__('Magento Only')),
      array('value' => 'social', 'label'=>Mage::helper('adminhtml')->__('Magento + Social Login')),
			array('value' => 'raas', 'label'=>Mage::helper('adminhtml')->__('Registration-as-a-Service')),
    );
/*      foreach ($opts as $opt) {
          $obj = new Varien_Object();
          $obj->setValue($opt['value']);
          $obj->setLabel($opt['label']);
          $obj->setStyle('float: left; clear: both;');
          $objs[] = $obj;
      }*/
      return $opts;
  }
}
