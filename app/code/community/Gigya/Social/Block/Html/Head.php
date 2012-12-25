<?php
/**
 *
 */
class Gigya_Social_Block_Html_Head extends Mage_Page_Block_Html_Head
{

  protected function _construct()
  {
    if (Mage::helper('Gigya_Social')->isPluginEnabled('gigya_global/gigya_global_conf')) {
      $this->setTemplate('page/html/head.phtml');
      $uriPrefix = !empty($_SERVER['HTTPS']) ? 'https://cdns' : 'http://cdn';
      $gigyaApiKey = trim(Mage::getStoreConfig('gigya_global/gigya_global_conf/apikey'));
      $name = $uriPrefix . '.gigya.com/JS/socialize.js?apikey=' . $gigyaApiKey;
      $jsParams = array(
        'enabledProviders' => (Mage::getStoreConfig('gigya_global/gigya_global_conf/providers') !== '') ? Mage::getStoreConfig('gigya_global/gigya_global_conf/providers') : '*',
        'lang' => Mage::getStoreConfig('gigya_global/gigya_global_conf/laguages'),
        'sessionExpiration' => (int)Mage::getStoreConfig('web/cookie/cookie_lifetime'),
        'connectWithoutLoginBehavior' => Mage::getStoreConfig('gigya_login/gigya_login_conf/loginBehavior'),
      );
      $this->_data['items']['js/gigya'] = array(
        'type' => 'external_js',
        'name' => $name,
        'if' => '',
        'cond' => '',
        'params' => Mage::helper('core')->jsonEncode($jsParams),
      );
      if (Mage::getStoreConfig('gigya_global/gigya_global_conf/google_sa')) {
        $ga = $uriPrefix . '.gigya.com/js/gigyaGAIntegration.js';
      $this->_data['items']['js/gigyaGA'] = array(
        'type' => 'external_js',
        'name' => $ga,
        'if' => '',
        'cond' => '',
      );

      }
    }
  }
  protected function _separateOtherHtmlHeadElements(&$lines, $itemIf, $itemType, $itemParams, $itemName, $itemThe)
    {
        $params = $itemParams ? ' ' . $itemParams : '';
        $href   = $itemName;
        switch ($itemType) {
            case 'rss':
                $lines[$itemIf]['other'][] = sprintf('<link href="%s"%s rel="alternate" type="application/rss+xml" />',
                    $href, $params
                );
                break;
            case 'link_rel':
                $lines[$itemIf]['other'][] = sprintf('<link%s href="%s" />', $params, $href);
                break;

            case 'external_js':
                $lines[$itemIf]['other'][] = sprintf('<script type="text/javascript" src="%s">%s</script>', $href, $params);
                break;

        }
    }

    public function getCssJsHtml()
    {
        // separate items by types
        $lines  = array();
        foreach ($this->_data['items'] as $item) {
            if (!is_null($item['cond']) && !$this->getData($item['cond']) || !isset($item['name'])) {
                continue;
            }
            $if     = !empty($item['if']) ? $item['if'] : '';
            $params = !empty($item['params']) ? $item['params'] : '';
            switch ($item['type']) {
                case 'js':        // js/*.js
                case 'skin_js':   // skin/*/*.js
                case 'js_css':    // js/*.css
                case 'skin_css':  // skin/*/*.css
                    $lines[$if][$item['type']][$params][$item['name']] = $item['name'];
                    break;
                default:
                    $this->_separateOtherHtmlHeadElements($lines, $if, $item['type'], $params, $item['name'], $item);
                    break;
            }
        }

        // prepare HTML
        $shouldMergeJs = Mage::getStoreConfigFlag('dev/js/merge_files');
        $shouldMergeCss = Mage::getStoreConfigFlag('dev/css/merge_css_files');
        $html   = '';
        foreach ($lines as $if => $items) {
            if (empty($items)) {
                continue;
            }
            if (!empty($if)) {
                $html .= '<!--[if '.$if.']>'."\n";
            }

            // static and skin css
            $html .= $this->_prepareStaticAndSkinElements('<link rel="stylesheet" type="text/css" href="%s"%s />' . "\n",
                empty($items['js_css']) ? array() : $items['js_css'],
                empty($items['skin_css']) ? array() : $items['skin_css'],
                $shouldMergeCss ? array(Mage::getDesign(), 'getMergedCssUrl') : null
            );
            // other stuff
            if (!empty($items['other'])) {
                $html .= $this->_prepareOtherHtmlHeadElements($items['other']) . "\n";
            }

            // static and skin javascripts
            $html .= $this->_prepareStaticAndSkinElements('<script type="text/javascript" src="%s"%s></script>' . "\n",
                empty($items['js']) ? array() : $items['js'],
                empty($items['skin_js']) ? array() : $items['skin_js'],
                $shouldMergeJs ? array(Mage::getDesign(), 'getMergedJsUrl') : null
            );


            if (!empty($if)) {
                $html .= '<![endif]-->'."\n";
            }
        }
        return $html;
    }
}
