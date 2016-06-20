<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 6/13/16
 * Time: 4:44 PM
 */
require_once('Mage/Customer/controllers/AccountController.php');
class Gigya_AutoTests_AccountController extends Mage_Core_Controller_Front_Action
{
    private $customer;

    public function preDispatch()
    {
        parent::preDispatch();
    }
    
    public function jsonAction()
    {
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $commonAccount = array();
        $this->customer = $this->_getSession()->getCustomer();
        $commonAccount['firstName'] = $this->_getData("firstname");
        $commonAccount['lastName'] = $this->_getData("lastname");
        $commonAccount['email'] = $this->_getData("email");
        $commonAccount['GUID'] = $this->_getData("gigya_uid");
        $commonAccount['isLoggedIn'] = empty($this->customer->getData("entity_id")) ? false : true;


        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($commonAccount));
        
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    private function _getData($key, $default = null)
    {
        return empty($this->customer->getData($key)) ? $default : $this->customer->getData($key);
    }

}