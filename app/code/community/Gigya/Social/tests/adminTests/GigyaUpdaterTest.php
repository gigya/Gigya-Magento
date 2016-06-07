<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 6/7/16
 * Time: 1:14 PM
 */
class GigyaUpdaterTest extends PHPUnit_Framework_TestCase
{

    private $magentoAccount;

    protected function setUp()
    {
        $this->magentoAccount = new Mage_Customer_Model_Customer();
        $this->magentoAccount->setData("gigya_uid", "test_gigya_uid");
    }

}
