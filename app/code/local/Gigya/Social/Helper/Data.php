<?php
class Gigya_Social_Helper_Data extends Mage_Core_Helper_Abstract
{
  public function _getPassword($length = 8)
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str = '';
    for ($p = 0; $p < $length; $p++) {
        $str .= $characters[mt_rand(0, strlen($characters))];
    }
      return 'Gigya_' . $str;
  }


}
