<?php

function upgrade_module_1_3_0($module)
{
    include dirname(__FILE__) . '/../sql/install.php';

    return installSql()
        && $module->registerHook([
            'displayAdminProductsExtra',
            'displayAdminProductsMainStepLeftColumnMiddle',
            'actionProductUpdate',
            'actionAdminProductsControllerSaveAfter',
            'actionProductFormBuilderModifier',
            'actionAfterCreateProductFormHandler',
            'actionAfterUpdateProductFormHandler',
            'displayAdminOrderTabContent',
            'displayAdminOrder',
        ]);
}
