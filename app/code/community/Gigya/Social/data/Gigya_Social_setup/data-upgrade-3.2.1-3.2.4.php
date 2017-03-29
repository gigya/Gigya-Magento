<?php
$syncIdentifier = Mage::getStoreConfig('gigya_login/gigya_raas_conf/gigya_sync_base');
if(empty($syncIdentifier)){
    Mage::getConfig()->saveConfig('gigya_login/gigya_raas_conf/gigya_sync_base', 'LoginIDsEmail');
}
