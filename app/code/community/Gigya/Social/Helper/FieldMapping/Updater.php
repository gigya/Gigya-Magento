<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 8/7/16
 * Time: 2:04 PM
 */
abstract class Gigya_Social_Helper_FieldMapping_Updater
{


    protected function retrieveFieldMappings()
    {
        /** @var Mage_Core_Model_Cache $cache */
        $cache = Mage::app()->getCache();
        $conf = $cache->load('gigya_field_mapping');
        if ($conf === false) {
            if ($this->path->hasChildren()) {
                $files = (array)$this->path->children();
                /** @var array $mappingArray */
                $mappingArray = array();
                foreach ($files as $file) {
                    $mappingArray = array_merge(
                        $mappingArray, json_decode($this->retrieveFieldMappingsFile($file), true)
                    );
                }
            } else {
                $mappingArray = json_decode($this->retrieveFieldMappingsFile(trim((string)$this->path)), true);
            }
            $conf = new Gigya_Social_Helper_FieldMapping_Conf($mappingArray);
            $cache->save(serialize($conf), "gigya_field_mapping", array("gigya"), 86400);
        } else {
            $conf = unserialize($conf);
        }
        return $conf;
    }

    /**
     * @param string $filePath
     *
     * @return null| string
     */
    private function retrieveFieldMappingsFile($filePath)
    {
        $mappingJson = file_get_contents($filePath);
        if (false === $mappingJson) {
            $err = error_get_last();
            $message
                 = "Could not retrieve field mapping configuration file. message was:"
                . $err['message'];
            Mage::log($message, Zend_Log::ERR);
            return null;
        }
        return $mappingJson;
    }

}