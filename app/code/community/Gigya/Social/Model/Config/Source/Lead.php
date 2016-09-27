<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 9/26/16
 * Time: 1:05 PM
 */
class Gigya_Social_Model_Config_Source_Lead
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'gigya', 'label'=>Mage::helper('adminhtml')->__('Gigya')),
            array('value' => 'magento', 'label'=>Mage::helper('adminhtml')->__('Magento')),
        );
    }

}