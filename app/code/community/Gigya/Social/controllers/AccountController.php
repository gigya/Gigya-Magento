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
            $valid = Mage::helper('Gigya_Social')->validateGigyaUid($post['UID'], $post['UIDSignature'], $post['signatureTimestamp']);
            if ($valid) {
                /** @var $customer Mage_Customer_Model_Customer */
                $customer = $this->_getSession()->getCustomer();
                $guid = $customer->getData("gigya_uid");
                $gigyaAccount = Mage::helper('Gigya_Social')->utils->getAccount($guid);
                $fname = $gigyaAccount['profile']['firstName'];
                $lName = $gigyaAccount['profile']['lastName'];
                $customer->setData('firstname', $fname);
                $customer->setData('lastname', $lName);
                $updater = new Gigya_Social_Helper_FieldMapping_MagentoUpdater($gigyaAccount);
                if ($updater->isMapped()) {
                    $updater->updateMagentoAccount($customer);
                }
            }
        }
    }


} 