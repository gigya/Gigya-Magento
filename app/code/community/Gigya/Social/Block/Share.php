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
      if ($pid = Mage::getSingleton('checkout/session')->getData('gigyaShare')) {
        $config = Mage::helper('Gigya_Social')->getPluginConfig('gigya_share/gigya_share_action');
        $product = Mage::getModel('catalog/product')->load($pid);
        $ua = Mage::helper('core')->jsonEncode(array(
          'title'       => $product->getName(),
          'description' => $product->getShortDescription(),
          'linkBack'    => $product->getProductUrl(),
          'imageUrl'    => $product->getImageUrl()
        ));

        $js = '
        var gigyaSettings = gigyaSettings || {};
        gigyaSettings.shareAction = ' . $config . ';
        gigyaSettings.shareAction.ua = ' . $ua .';
        ';
        $this->setContents($js);
        Mage::getSingleton('checkout/session')->unsetData('gigyaShare');
        return  parent::_toHtml();
      }
    }
  }

}


