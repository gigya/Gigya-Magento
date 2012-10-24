<?php
class Gigya_Social_Block_Adminhtml_Form_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->_blockGroup = 'Gigya_Social';
    $this->_controller = 'adminhtml_form';
    $this->_headerText = Mage::helper('gigya_social')->__('Edit Form');
  }
}

