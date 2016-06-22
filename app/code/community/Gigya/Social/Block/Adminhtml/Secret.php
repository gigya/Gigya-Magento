<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 5/22/16
 * Time: 5:42 PM
 */
class Gigya_Social_Block_Adminhtml_Secret extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     * @inheritDoc
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $masked = Mage::helper('Gigya_Social')->buildMask($element->getValue());
        $element->addData(array("after_element_html" => '<span>' . $masked . '</span>'));
        $html = $element->getElementHtml();
        return $html;
    }





}