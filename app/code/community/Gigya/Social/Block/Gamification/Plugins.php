<?php
/**
 * Class Gigya_Social_Block_Gamification_Plugins
 * @author  Yaniv Aran-Shamir
 */
class Gigya_Social_Block_Gamification_Plugins extends Mage_Core_Block_Template
{

  protected function _beforeToHtml()
  {
    $validPlugins = array('Achievements', 'ChallengeStatus', 'UserStatus', 'Leaderboard');
    $layout = $this->getData('layout');
    $html = '';
    foreach ($layout as $plugin => $divId) {
      if (in_array($plugin, $validPlugins)) {
        $html .= '<div id="' . $divId . '" class="gigya-' . $plugin . '"></div>' . PHP_EOL;
      }
      else {
        unset($layout['$plugin']);
      }
    }
    $this->setHtml($html);
    $this->setJs(Mage::helper('core')->jsonEncode($layout));
  }
}
