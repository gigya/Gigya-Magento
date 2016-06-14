<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 6/13/16
 * Time: 4:44 PM
 */
require_once('Mage/Customer/controllers/AccountController.php');
class Gigya_AutoTests_AccountController extends Mage_Customer_AccountController
{

    public function preDispatch()
    {
        // a brute-force protection here would be nice

        parent::preDispatch();

        if ( ! $this->getRequest()->isDispatched()) {
            return;
        }

        $action      = $this->getRequest()->getActionName();
        $openActions = array(
            'create',
            'login',
            'logout',
            'loginPost',
            'logoutsuccess',
            'forgotpassword',
            'forgotpasswordpost',
            'resetpassword',
            'resetpasswordpost',
            'confirm',
            'confirmation',
            'getaccount'
        );
        $pattern     = '/^(' . implode('|', $openActions) . ')/i';

        if ( ! preg_match($pattern, $action)) {
            if ( ! $this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }
    public function getaccountAction()
    {
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $customer = $this->_getSession()->getCustomer();
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($customer));
        
    }

}