<?php

$installer = $this;

$installer->startSetup();
$setup = Mage::getModel('customer/entity_setup', 'core_setup');
$setup->addAttribute('customer', 'gigya_uid', array(
	'type' => 'varchar',
	'input' => 'text',
	'label' => 'Gigya User ID',
	'global' => 1,
	'visible' => 0,
	'required' => 0,
	'user_defined' => 1,
	'default' => '',
	'visible_on_front' => 0,
));


if (version_compare(Mage::getVersion(), '1.6.0', '<='))
{
	$customer = Mage::getModel('customer/customer');
	$attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
	$setup->addAttributeToSet('customer', $attrSetId, 'General', 'gigya_uid');
}

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
	Mage::getSingleton('eav/config')
	->getAttribute('customer', 'gigya_uid')
	->setData('used_in_forms', array('adminhtml_customer','customer_account_create','customer_account_edit','checkout_register'))
	->save();

}
$installer->endSetup();