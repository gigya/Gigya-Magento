<?php
class Gigya_Social_Module_Config_Source_Languages
{
  /**
   * Options getter
   *
   * @return array
   */
  public function toOptionArray()
  {
    return array(
      array('value' => 'en', 'label'=>Mage::helper('adminhtml')->__('English')),
      array('value' => 'zh-cn', 'label'=>Mage::helper('adminhtml')->__('Chinese')),
      array('value' => 'zh-hk', 'label'=>Mage::helper('adminhtml')->__('Chinese(HongKong)')),
      array('value' => 'zh-tw', 'label'=>Mage::helper('adminhtml')->__('Chinese(Taiwan)')),
      array('value' => 'cs', 'label'=>Mage::helper('adminhtml')->__('Czech')),
      array('value' => 'da', 'label'=>Mage::helper('adminhtml')->__('Danish')),
      array('value' => 'nl', 'label'=>Mage::helper('adminhtml')->__('Dutch')),
      array('value' => 'fi', 'label'=>Mage::helper('adminhtml')->__('Finnish')),
      array('value' => 'fr', 'label'=>Mage::helper('adminhtml')->__('French')),
      array('value' => 'de', 'label'=>Mage::helper('adminhtml')->__('German')),
      array('value' => 'el', 'label'=>Mage::helper('adminhtml')->__('Greek')),
      array('value' => 'hu', 'label'=>Mage::helper('adminhtml')->__('Hungarian')),
      array('value' => 'id', 'label'=>Mage::helper('adminhtml')->__('Indonesian')),
      array('value' => 'it', 'label'=>Mage::helper('adminhtml')->__('Italian')),
      array('value' => 'ja', 'label'=>Mage::helper('adminhtml')->__('Japanese')),
      array('value' => 'ko', 'label'=>Mage::helper('adminhtml')->__('Korean')),
      array('value' => 'ms', 'label'=>Mage::helper('adminhtml')->__('Malay')),
      array('value' => 'no', 'label'=>Mage::helper('adminhtml')->__('Norwegian')),
      array('value' => 'pl', 'label'=>Mage::helper('adminhtml')->__('Polish')),
      array('value' => 'pt', 'label'=>Mage::helper('adminhtml')->__('Portuguese')),
      array('value' => 'pt-br', 'label'=>Mage::helper('adminhtml')->__('Portuguese(Brazil)')),
      array('value' => 'ro', 'label'=>Mage::helper('adminhtml')->__('Romanian')),
      array('value' => 'ru', 'label'=>Mage::helper('adminhtml')->__('Russian')),
      array('value' => 'es', 'label'=>Mage::helper('adminhtml')->__('Spanish')),
      array('value' => 'es-mx', 'label'=>Mage::helper('adminhtml')->__('Spanish(Mexican)')),
      array('value' => 'sv', 'label'=>Mage::helper('adminhtml')->__('Swedish')),
      array('value' => 'tl', 'label'=>Mage::helper('adminhtml')->__('Tagalog(Philippines)')),
      array('value' => 'th', 'label'=>Mage::helper('adminhtml')->__('Thai')),
      array('value' => 'tr', 'label'=>Mage::helper('adminhtml')->__('Turkish')),
      array('value' => 'uk', 'label'=>Mage::helper('adminhtml')->__('Ukrainian')),
      array('value' => 'vi', 'label'=>Mage::helper('adminhtml')->__('Vietnamese')),
    );
  }
}
