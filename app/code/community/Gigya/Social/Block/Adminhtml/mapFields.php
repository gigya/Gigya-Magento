<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 10/21/13
 * Time: 11:48 AM
 */

class Gigya_Social_Block_Adminhtml_Version extends Mage_Adminhtml_Block_System_Config_Form_Field {
  protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
  {
    return (string) Mage::helper('Gigya_Social')->getExtensionVersion();
  }

}