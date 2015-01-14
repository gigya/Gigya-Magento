<?php
/**
 * Class Gigya_Social_Block_Ordersharebar
 * @author  Yaniv Aran-Shamir
 */
class Gigya_Social_Block_Ordersharebar extends Mage_Core_Block_Template
{
  protected function _toHtml()
  {
    if (Mage::helper('Gigya_Social')->isShareBarEnabled('order')) {
      if ($share = Mage::getSingleton('checkout/session')->getData('gigyaShareOrder')) {
        $config = Mage::helper('Gigya_Social')->getPluginConfig('gigya_share/gigya_sharebar');
        $product = Mage::getModel('catalog/product')->load($share['pid']);
        $desc = ($product->getShortDescription() !== NULL) ? $product->getShortDescription() : $product->getDescription();
        $ua = Mage::helper('core')->jsonEncode(array(
          'title'       => $product->getName(),
          'description' => $this->stripTags($desc),
          'linkBack'    => $product->getProductUrl(),
          'imageUrl'    => $product->getImageUrl(),
        ));

        $js = '
        var gigyaMageSettings = gigyaMageSettings || {};
        gigyaMageSettings.sharebar = ' . $config . ';
        gigyaMageSettings.sharebar.ua = ' . $ua .';
        ';
        $this->setContents($js);
        Mage::getSingleton('checkout/session')->unsetData('gigyaShareOrder');
        return  parent::_toHtml();
      }
    }
  }

}


