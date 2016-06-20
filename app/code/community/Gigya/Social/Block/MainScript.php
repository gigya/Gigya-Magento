<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 5/24/16
 * Time: 9:45 AM
 */
class Gigya_Social_Block_MainScript extends Mage_Core_Block_Text_Tag_Js
{

    protected $gigyaApiKey;
    protected $lang;
    protected $fallbackLang;
    protected $globalConf;
    protected $userMode;
    protected $raasConf = null;
    protected $magentoLoggedIn;
    protected $baseUrl;
    protected $numOfRetries;

    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        if (Mage::helper('Gigya_Social')->isPluginEnabled('gigya_global/gigya_global_conf')) {

            $this->gigyaApiKey = trim(Mage::getStoreConfig('gigya_global/gigya_global_conf/apikey')); // get api key from store
            $this->lang        = Mage::getStoreConfig('gigya_global/gigya_global_conf/laguages');
            $this->fallbackLang        = Mage::getStoreConfig('gigya_global/gigya_global_conf/fallback_lang');
            if ("auto" == $this->lang) {
                $locale = Mage::app()->getLocale()->getLocaleCode();
                $this->lang = $this->magentoLocaleToGigyaLang($locale, $this->fallbackLang);
            }
            $this->globalConf  = array( // set config basic params (enabledProviders, lang, sessionExpiration, connectWithoutLoginBehavior)
                'enabledProviders'            => (Mage::getStoreConfig('gigya_global/gigya_global_conf/providers') !== '') ? Mage::getStoreConfig('gigya_global/gigya_global_conf/providers') : '*',
                'lang'                        => $this->lang,
                'sessionExpiration'           => (int) Mage::getStoreConfig('web/cookie/cookie_lifetime'),
                'connectWithoutLoginBehavior' => Mage::getStoreConfig('gigya_global/gigya_global_conf/loginBehavior'),
            );
            $advanced_config   = Mage::getStoreConfig('gigya_global/gigya_global_conf/advancedConfig');
            if ($advanced_config !== '') {
                $advanced_config_arr = Mage::helper('Gigya_Social')->getGigGlobalAdvancedConfig($advanced_config);
                if ( ! $advanced_config_arr) {
                    $advanced_config_arr = Mage::helper('Gigya_Social')->_confStringToArry($advanced_config);
                }
                $this->globalConf = array_merge($this->globalConf, $advanced_config_arr);
            }
            $this->userMode = Mage::getStoreConfig('gigya_login/gigya_user_management/login_modes');
            if ("raas" == $this->userMode) {
                $this->raasConf = Mage::helper('Gigya_Social')->getPluginConfig('gigya_login/gigya_raas_conf');

            }
            $this->magentoLoggedIn = Mage::getSingleton('customer/session')->isLoggedIn() ? "true" : "false";
            $isSecure = isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : NULL;
            if ($isSecure) {
                $this->baseUrl = Mage::getUrl('', array('_secure'=>true));
            } else {
                $this->baseUrl =  Mage::getBaseUrl();
            }
            $this->numOfRetries = Mage::getStoreConfig('gigya_global/gigya_global_conf/login_retries');
        }

        return $this;

    }

    protected function _toHtml()
    {
        if ( ! empty($this->gigyaApiKey)) {
            $js = "window.__gigyaConf = " . json_encode($this->globalConf) . "
            var gig = document.createElement('script');
            gig.type = 'text/javascript';
            gig.async = true;
            gig.src = ('https:' == document.location.protocol ? 'https://cdns' : 'http://cdn') + '.gigya.com/js/gigya.js?apiKey=" .
                $this->gigyaApiKey . "&lang=" . $this->lang . "';
            document.getElementsByTagName('head')[0].appendChild(gig);
            var gigyaMageSettings = gigyaMageSettings || {};
            gigyaMageSettings.userMode = '" . $this->userMode . "';
            gigyaMageSettings.magentoStatus = '" . $this->magentoLoggedIn . "';
            var baseUrl = '" . $this->baseUrl . "';";
            if (null != $this->raasConf) {
                $js = $js . "gigyaMageSettings.RaaS = " . $this->raasConf;
            }
            $this->setContents($js);
        }
        return  parent::_toHtml();
    }

    protected function magentoLocaleToGigyaLang($locale, $default = "en")
    {
        
        $gigyaLangs = array();
        foreach (Mage::helper('Gigya_Social')->getGigyaLanguages() as $l) {
            $gigyaLangs[$l] = $l;
        }
        $lang = null;
        $glocale = str_replace("_", "-", strtolower($locale));
        $lang = $gigyaLangs[$glocale];
        if (null == $lang) {
            $lang = $gigyaLangs[substr($locale, 0, 2)];
        }
        return empty($lang) ? $default : $lang;

    }

}