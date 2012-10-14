<?php

class Gigya_Social_AdminController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('gigya');
        $block = $this->getLayout()
          ->createBlock('core/text', 'Giyag Gloabl Configuration')
          ->setText('<h1>Gigya global configuration</h1>');
        $this->_addContent($block);
        $this->_addBreadcrumb(Mage::helper('Gigya_Social')->__('Form'), Mage::helper('Gigya_Social')->__('Form'));
        Mage::log(var_export($this, TRUE));
        // "Output" display
        $this->renderLayout();
    }	
    public function shareAction()
    {
    	// "Fetch" display
        $this->loadLayout();
        $this->_setActiveMenu('gigya');

        // "Inject" into display
        // THe below example will not actualy show anything since the core/template is empty
        $this->_addContent($this->getLayout()->createBlock('core/template'));

         echo "Hello developer...";

        // "Output" display
        $this->renderLayout();
    }	
}
