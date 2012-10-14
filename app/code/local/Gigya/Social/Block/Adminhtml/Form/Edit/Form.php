<?php
class Gigya_Social_Block_Adminhtml_Form_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
/**
* Preparing form
*
* @return Mage_Adminhtml_Block_Widget_Form
*/
protected function _prepareForm()
{
$form = new Varien_Data_Form(
array(
'id' => 'edit_form',
'action' => $this->getUrl('*/*/save'),
'method' => 'post',
)
);

$form->setUseContainer(true);
$this->setForm($form);

$helper = Mage::helper('Gigya_Social');
$fieldset = $form->addFieldset('display', array(
'legend' => $helper->__('Display Settings'),
'class' => 'fieldset-wide'
));

$fieldset->addField('label', 'text', array(
'name' => 'label',
'label' => $helper->__('Label'),
));

if (Mage::registry('Gigya_Social')) {
$form->setValues(Mage::registry('turnkeye_adminform')->getData());
}

return parent::_prepareForm();
}
}

