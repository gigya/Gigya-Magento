<?php
/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 6/26/16
 * Time: 1:12 PM
 */

$key = Mage::getStoreConfig('gigya_global/gigya_global_conf/secretkey');
$enc = Mage::helper('core')->encrypt($key);
Mage::getConfig()->saveConfig('gigya_global/gigya_global_conf/secretkey', $enc);
