<?php
/**
 * Class Gigya_Social_Block_Share
 * @author  Yaniv Aran-Shamir
 */
class Gigya_Social_Block_Share extends Mage_Core_Block_Text_Tag_Js
{
  protected function _toHtml()
  {
    if (Mage::helper('Gigya_Social')->isPluginEnabled('gigya_share/gigya_share_action')) {
      if ($share = Mage::getSingleton('checkout/session')->getData('gigyaShare')) {
        $config = Mage::helper('Gigya_Social')->getPluginConfig('gigya_share/gigya_share_action', 'json', TRUE);
        $product = Mage::getModel('catalog/product')->load($share['pid']);
        $desc = ($product->getShortDescription() !== NULL) ? $product->getShortDescription() : $product->getDescription();
        $action = ($share['op'] === 'cart') ? $this->__('Added to cart') : $this->__('Ordered');
        $ua = Mage::helper('core')->jsonEncode(array(
          'title'       => $product->getName(),
          'description' => $this->stripTags($desc),
          'linkBack'    => $product->getProductUrl(),
          'imageUrl'    => $product->getImageUrl(),
          'action'      => $action,
        ));

        $js = '
        var gigyaMageSettings = gigyaMageSettings || {};
        gigyaMageSettings.shareAction = ' . $config . ';
        gigyaMageSettings.shareAction.ua = ' . $ua .';
        ';
        $this->setContents($js);
        Mage::getSingleton('checkout/session')->unsetData('gigyaShare');
        return  parent::_toHtml();
      }
    }
  }

}


