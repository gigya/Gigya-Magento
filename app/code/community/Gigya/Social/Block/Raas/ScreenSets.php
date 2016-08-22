<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 7/24/16
 * Time: 12:07 PM
 */
class Gigya_Social_Block_Raas_ScreenSets extends Mage_Core_Block_Template
{

    protected function _beforeToHtml()
    {
        $params = $this->getData("params");
        if (count($params) > 0) {
            $embed = isset($params['containerID']);
            $this->setEmbed($embed);
            if (!$embed) {
                $linkText = empty($params['text'])
                    ? "Please add the text parameter to the block" : $params['text'];
                $html     = '<a class="gigya-screenset-link" onclick="showScreens()" href="#">' . $linkText . '</a>';
            } else {
                $html = '<div id="' . $params['containerID'] . '"></div>';
            }
            unset($params['text']);
            $jsParams = json_encode($params);
            $this->setParams($jsParams);
            $this->setHtml($html);
        }
    }

    /**
     * For onepage checkout
     * Retrieve is allow and show block
     *
     * @return bool
     */
    public function isShow()
    {
        return true;
    }

}