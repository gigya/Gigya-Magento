<?php
/**
 * Class Gigya_Social_Model_Cart_Observer
 * @author  Yaniv Aran-Shamir
 */
class Gigya_Social_Model_Cart_Observer
{
  public function addShareUi($observer)
  {
    if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_add') {
      $parms = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
      $config = Mage::helper('Gigya_Social')->getPluginConfig('gigya_share/gigya_share_action');
      $layout = $observer->getEvent()->getControllerAction()->getLayout();
      $product = Mage::getModel('catalog/product')->load($parms['product']);
      $ua = Mage::helper('core')->jsonEncode(array(
        'title'       => $product->getName(),
        'description' => $product->getShortDescription(),
        'linkBack'    => $product->getProductUrl(),
        'imageUrl'    => $product->getImageUrl()
      ));
      $block = $layout->createBlock('core/text');
      $block->setText(
        '<script type="text/javascript">
        var gigyaSettings = gigyaSettings || {};
        gigyaSettings.shareAction = ' . $config . ';
        gigyaSettings.shareAction.userAction = ' . $ua .';
        </script>'
      );
        //$layout->getBlock('content')->append($block);
        $b = $observer->getEvent();

        //Mage::log(get_class_methods($layout->getBlock('content')));
        Mage::log($b);
        //Mage::log($layout->getOutput());
      //Mage::log($observer->getEvent()->getControllerAction()->getLayout());
    }
  }
}


