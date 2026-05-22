<?php

if (!function_exists('hscodesColumnExistsForUninstall')) {
    function hscodesColumnExistsForUninstall($column)
    {
        $column = pSQL($column);

        return (bool) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = \'' . pSQL(_DB_PREFIX_ . 'product') . '\'
               AND COLUMN_NAME = \'' . $column . '\''
        );
    }
}

if (!function_exists('uninstallSql')) {
    function uninstallSql()
    {
        $queries = [];

        if (hscodesColumnExistsForUninstall('hscode')) {
            $queries[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'product` DROP COLUMN `hscode`';
        }

        if (hscodesColumnExistsForUninstall('origin')) {
            $queries[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'product` DROP COLUMN `origin`';
        }

        foreach ($queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }
}
