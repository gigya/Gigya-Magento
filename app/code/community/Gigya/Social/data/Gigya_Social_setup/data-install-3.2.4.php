<?php
/* adding the deference for Gigya UID vs Gigya LoginIdsEmail */
Mage::getConfig()->saveConfig('gigya_login/gigya_raas_conf/gigya_sync_base', 'GigyaUID');