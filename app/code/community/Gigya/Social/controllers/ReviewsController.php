<?php
/**
 * Class Gigya_Social_ReviewsController
 * @author  Yaniv Aran-Shamir
 * Accept review data by ajax call from: gigya.js - gigyaFunctions.postReview
 *
 */
include_once __DIR__ . '/../sdk/GSSDK.php';
require_once ('Mage/Review/controllers/ProductController.php');

class Gigya_Social_ReviewsController  extends Mage_Review_ProductController
{

  public function indexAction()
  {
    $this->loadLayout();
    $this->renderLayout();
  }
    /**
     * Submit new review action
     *
     */
    public function postAction()
    {
        if ($data = Mage::getSingleton('review/session')->getFormData(true)) {
            $rating = array();
            if (isset($data['ratings']) && is_array($data['ratings'])) {
                $rating = $data['ratings'];
            }
        } else {
            $data   = $this->getRequest()->getPost();
            $data = Mage::helper('core')->jsonDecode($data['json']);
            $rating = array_filter($data['ratings']);
        }

        if (($product = $this->_initProduct()) && !empty($data)) {
            $session    = Mage::getSingleton('core/session');
            /* @var $session Mage_Core_Model_Session */
            $review     = Mage::getModel('review/review')->setData($data);
            /* @var $review Mage_Review_Model_Review */

            $res = array();
            $validate = $review->validate();
            if ($validate === true) {
                // if verified purchaser badge exists,
                //   check if customer is a purchaser.
                //     if it is add badge to review
                $cat_badge = $this->_catVerifiedBadge($data['categoryID']);
                if ($cat_badge) {
                    $verified_purchaser = $this->_is_verified_purchaser($data['user'], $product->getId());
                    if ($verified_purchaser) {
                        $badge_added = $this->_addVerifiedPurchaserBadge();
                        if (!$badge_added) {
                            // verified purchaser badge is on and customer is a purchaser,
                            // but badge adding failed - generate log error
                        }
                    }
                }


                try {
                    $review->setEntityId($review->getEntityIdByCode(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE))
                        ->setEntityPkValue($product->getId())
                        ->setStatusId(Mage_Review_Model_Review::STATUS_PENDING)
                        ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->setStores(array(Mage::app()->getStore()->getId()))
                        ->save();

                    foreach ($rating as $ratingId => $optionId) {
                        Mage::getModel('rating/rating')
                        ->setRatingId($ratingId)
                        ->setReviewId($review->getId())
                        ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                        ->addOptionVote($optionId, $product->getId());
                    }

                    $review->aggregate();
                    $res['result'] = 'ok';
                    $res['message'] = $this->__('Your review has been accepted for moderation.');
                }
                catch (Exception $e) {
                    $session->setFormData($data);
                    $res['result'] = 'error';
                    $res['message'] = $this->__('Unable to post the review.');
                }
            }
            else {
                $session->setFormData($data);
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        $res['message'][] = $errorMessage;
                    }
                }
                else {
                    $session->addError($this->__('Unable to post the review.'));
                    $res['message'] = $this->__('Unable to post the review.');
                }
                  $res['result'] = 'error';
            }
        }
        else {
          $res = array(
            'result' => 'error',
            'message' => $this->__('No post data.'),
          );

        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($res));
    }

  /*
   * Check with Gigya if the ratings category has a verified purchaser badge set (highlightSettings)
   * @param int @catID
   * return bool $badge_cat_exists
   */
  protected function _catVerifiedBadge($catID) {
     $badge_cat_exists = false;
     $cat_info = Mage::helper('Gigya_Social')->utils->getCommentsCategoryInfo($catID);

     if (is_array($cat_info)) {
         $arr_highlightGroups = $cat_info['category']['highlightSettings']['groups'];
         foreach ( $arr_highlightGroups as $array ) {
             if ($array['name'] === "Verified-Purchaser" ) {
                 $badge_cat_exists = true;
                 // check if badge is enabled
                 continue;
             }
         }
     } elseif (is_numeric($cat_info)) {
         // an error returned, meaning category info could not be retrieved
         // we will log it and continue without marking verified user.
         Mage::log('Could not retrieve category info. error code:'. $cat_info['errorCode'] . __FILE__ . __LINE__);
         return false;
     } else {
         Mage::log('Could not retrieve category info.' .  __FILE__ . __LINE__);
     }

     return $badge_cat_exists;
  }

  /*
   * check if reviewing customer has purchased that product
   *  get all user orders
   *  in each order get all item id's
   *  compare item id with current review item id
   *
   * @param int UID
   * @param int $productId
   * @return bool $verified
   */
    protected function _is_verified_purchaser($uid, $productId) {
        $purchaser = false;
        $user_orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', $uid);
        foreach ($user_orders as $order ) {
            $items = $order->getAllVisibleItems();
            foreach ( $items as $item) {
                $prodId = $item->getProductId();
                if ($prodId == $productId) {
                    $purchaser = true;
                    continue;
                }
            }
        }
        return $purchaser;
    }

    /*
     * add verified purchaser badge to comment
     *
     * @param bool $badge_added
     */
    protected function _addVerifiedPurchaserBadge() {
        $badge_added = false;
        return $badge_added;
    }
}


