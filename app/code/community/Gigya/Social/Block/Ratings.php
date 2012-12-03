<?php
/**
 * Class Gigya_Social_Block_Ratings
 * @author  Yaniv Aran-Shamir
 */
class Gigya_Social_Block_Ratings extends Mage_Catalog_Block_Product_View
{
  protected function _toHtml()
  {
    if (Mage::helper('Gigya_Social')->isPluginEnabled('gigya_r_and_r/gigya_r_and_r_conf')) {
      $product = $this->getProduct();
      $parms = Mage::helper('Gigya_Social')->getPluginConfig('gigya_r_and_r/gigya_r_and_r_conf', 'php');
      Mage::log($parms);
      unset($parms['enable']);
      $parms['streamID'] = $product->getSku();
      $parms['context']['reviewUrl'] = $this->getReviewsUrl();
      $js = '<script type="text/javascript">//<![CDATA[
        var gigyaSettings = gigyaSettings || {};
      gigyaSettings.ratings = gigyaSettings.ratings || [];
      gigyaSettings.ratings.push(' . Mage::helper('core')->jsonEncode($parms) . ');
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


