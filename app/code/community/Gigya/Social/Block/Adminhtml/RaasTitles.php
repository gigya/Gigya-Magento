<?php
/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 6/9/14
 * Time: 1:37 PM
 */

class Gigya_Social_Block_Adminhtml_RaasTitles extends Mage_Adminhtml_Block_System_Config_Form_Field {
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		switch ( $element->getId()) {
			case "gigya_login_gigya_raas_conf_title":
				return (string) '<span style="font-size: 14px;"><b>Login/Registration Screen Sets</b></span>';
		        break;
			case "gigya_login_gigya_raas_conf_profile_title":
				return (string) '<span style="font-size: 14px;"><b>Profile Screen Sets</b></span>';
				break;
			case "gigya_login_gigya_raas_conf_div_ids_title":
				return (string) '<span style="font-size: 14px;"><b>DIV IDs</b></span>';
				break;
            case "gigya_login_gigya_raas_conf_session_lead_title":
                return (string) '<span style="font-size: 14px;"><b>Session Lead</b></span>';
                break;

		}
	}

}
