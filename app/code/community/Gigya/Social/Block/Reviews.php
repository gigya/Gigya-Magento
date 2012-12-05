<?php
/**
 * Class Gigya_Social_Block_Reviews
 * @author  Yaniv Aran-Shamir
 */
class Gigya_Social_Block_Reviews extends Mage_Catalog_Block_Product_View
{
  protected function _toHtml()
  {
    if (Mage::helper('Gigya_Social')->isPluginEnabled('gigya_r_and_r/gigya_r_and_r_conf')) {
      $product = $this->getProduct();
      $desc = ($product->getShortDescription() !== NULL) ? $product->getShortDescription() : $product->getDescription();
      $ua = array(
        'title'       => $product->getName(),
        'description' => $this->stripTags($desc),
        'linkBack'    => $product->getProductUrl(),
        'imageUrl'    => $product->getImageUrl()
      );
      $parms = Mage::helper('Gigya_Social')->getPluginConfig('gigya_r_and_r/gigya_r_and_r_conf', 'php', TRUE);
      unset($parms['enable']);
      $parms['streamID'] = $product->getSku();
      $parms['ua'] = $ua;
      $js = '<script type="text/javascript">//<![CDATA[
        var gigyaSettings = gigyaSettings || {};
      gigyaSettings.RnR = ' . Mage::helper('core')->jsonEncode($parms) . '
        //]]>
      </script>';
return $js;
    }
  }
  public function getReviewsUrl()
  {
      return Mage::getUrl('review/product/list', array(
         'id'        => $this->getProduct()->getId(),
         'category'  => $this->getProduct()->getCategoryId()
      ));
  }

}


