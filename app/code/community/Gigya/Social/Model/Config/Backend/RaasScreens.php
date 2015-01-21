<?php
/**
 * Truncate whitespace before saving RaaS screen ID's
 */
class Gigya_Social_Model_Config_Backend_RaasScreens extends Mage_Core_Model_Config_Data
{
  protected function _beforeSave() {
    $trimmed_value = trim($this->getValue());
    $this->setValue($trimmed_value);
  }
}