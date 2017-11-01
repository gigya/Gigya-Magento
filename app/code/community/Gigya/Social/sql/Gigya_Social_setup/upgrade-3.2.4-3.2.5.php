<?php

$installer = $this;

$installer->startSetup();

$installer->updateAttribute('customer', 'gigya_uid', 'frontend_input', 'label');

$installer->endSetup();