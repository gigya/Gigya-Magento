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
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->_getSession()->getCustomer();
            $req = json_decode($this->getRequest()->getPost('json'));
            $fName = $req->profile->firstName;
            $lName = $req->profile->lastName;
            if (!empty($fName) && !empty($lName)) {
                $customer->firstname = $fName;
                $customer->lastname = $lName;
                $customer->save();
            } else {
                Mage::log("first name and last name should not be empty");
            }
        }
    }


} 