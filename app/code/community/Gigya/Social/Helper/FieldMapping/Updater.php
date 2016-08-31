<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 8/7/16
 * Time: 2:04 PM
 */
require_once Mage::getModuleDir('', 'Gigya_Social') . DS . 'sdk' . DS . 'gigyaCMS.php';
abstract class Gigya_Social_Helper_FieldMapping_Updater
{

    protected $path;

    protected function retrieveFieldMappings()
    {
        /** @var Mage_Core_Model_Cache $cache */
        $cache = Mage::app()->getCache();
        $conf = $cache->load($this->getCacheKey());
        if ($conf === false) {
            if ($this->path->hasChildren()) {
                $files = (array)$this->path->children();
                /** @var array $mappingArray */
                $mappingArray = array();
                foreach ($files as $file) {
                    $newMappings = GigyaCMS::parseJSON($this->retrieveFieldMappingsFile($file));
                    if (is_array($newMappings)) {
                        $mappingArray = array_merge($mappingArray, $newMappings);
                    } else {
                        Mage::log('Bad json in file ' . $file . 'error was ' . $newMappings, Zend_Log::ERR);
                    }
                }
            } else {
                $mappingArray = GigyaCMS::parseJSON($this->retrieveFieldMappingsFile(trim((string)$this->path)));
                if (!is_array($mappingArray)) {
                    Mage::log('Bad json in file ' . (string)$this->path  . 'error was ' . $mappingArray, Zend_Log::ERR);
                    $mappingArray = null;
                }
            }
            if (null != $mappingArray) {
                $conf = new Gigya_Social_Helper_FieldMapping_Conf($mappingArray);
                $cache->save(serialize($conf), $this->getCacheKey(), array("gigya"), 86400);
            }
        } else {
            $conf = unserialize($conf);
        }
        return $conf;
    }

    abstract protected function getCacheKey();

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