<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 7/7/14
 * Time: 1:17 PM
 */
require_once('Mage/Customer/controllers/AccountController.php');
class Gigya_Social_AccountController extends Mage_Customer_AccountController
{

    public function preDispatch()
    {
        parent::preDispatch();
    }

    public function editPostAction()
    {
        if ($this->getRequest()->isPost()) {
            $post = json_decode($this->getRequest()->getPost('json'), true);
            $valid = $this->validate($post);
            if ($valid) {
                /** @var $customer Mage_Customer_Model_Customer */
                $customer = $this->_getSession()->getCustomer();
                $guid = $customer->getData("gigya_uid");
                $gigyaAccount = $this->getGigyaAccount($guid);
                /** @var stdClass $accountObject
                 * we cast the array to object so it would be passed by reference and data
                 * could be added to it.
                 */
                $accountObject = (object) $gigyaAccount;
                Mage::dispatchEvent("gigya_post_account_fetch", array("gigyaAccount" => $accountObject));
                $gigyaAccount = (array) $accountObject;
                $fname = $gigyaAccount['profile']['firstName'];
                $lName = $gigyaAccount['profile']['lastName'];
                $customer->setData('firstname', $fname);
                $customer->setData('lastname', $lName);
                $updater = new Gigya_Social_Helper_FieldMapping_MagentoUpdater($gigyaAccount);
                if ($updater->isMapped()) {
                    $updater->updateMagentoAccount($customer);
                } else {
                    $customer->save();
                }
            }
        }
    }

    protected function getGigyaAccount($guid)
    {
        return Mage::helper('Gigya_Social')->utils->getAccount($guid);
    }

    protected function validate($post)
    {
        return Mage::helper('Gigya_Social')->validateGigyaUid($post['UID'], $post['UIDSignature'], $post['signatureTimestamp']);

    }

} 