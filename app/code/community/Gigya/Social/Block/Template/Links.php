<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 6/15/14
 * Time: 10:57 AM
 */
class Gigya_Social_Block_Template_Links extends Mage_Page_Block_Template_Links
{

    public function addParamsByUrl($url, $params, $ifConfig)
    {
        if (!empty($ifConfig)) {
            if (Mage::getStoreConfig($ifConfig['config']) == $ifConfig['value']) {
                $this->_addParam($url, $params);
            }
        } else {
            $this->_addParam($url, $params);
        }
            return $this;
        }

    protected function  _addParam($url, $params) {
        foreach ($this->_links as $k => $v) {
            if ($v->getUrl() == $url) {
                foreach ($params as $key => $value) {
                    if ($key == 'li_params' || $key == 'a_params') {
                        $value = $this->_prepareParams($value);
                    }
                    $v->setData($key, $value);
                }
                $this->_links[$k] = $v;
            }
        }

    }

}