<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 6/1/16
 * Time: 5:27 PM
 */
class bootstrap
{

    /**
     * bootstrap constructor.
     */
    public function __construct()
    {
        echo "Loading Magento\n";
        require_once( MAGENTO_ROOT . '/app/Mage.php' );
        Mage::app();

    }
}

// Autoload:
new Bootstrap();