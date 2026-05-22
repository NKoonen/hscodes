<?php

function upgrade_module_1_1_0($module)
{
    include dirname(__FILE__) . '/../sql/install.php';

    return installSql()
        && $module->registerHook('displayAdminOrderTabContent');
}
