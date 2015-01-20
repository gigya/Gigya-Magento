<?php
/**
 * Backend module for mapping user fields from Gigya to Magento
 * get mapped/deleted fields
 * update user object
 * save to config DB
 */
class Gigya_Social_Model_Config_Backend_FieldMap extends Mage_Core_Model_Config_Data
{
    /*
     * Update mapped fields values
     */
    protected function _beforeSave() {
        $attributes = Mage::getModel('customer/customer')->getAttributes();
        foreach ($attributes as $attr) {
        //    if ($attr->getId() == $this )
        }
    }

    protected function _afterSave() {

    }
}