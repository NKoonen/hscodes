<?php

if (!function_exists('hscodesColumnExists')) {
    function hscodesColumnExists($column)
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

if (!function_exists('installSql')) {
    function installSql()
    {
        $queries = [];

        if (!hscodesColumnExists('hscode')) {
            $queries[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'product` ADD `hscode` VARCHAR(32) NULL DEFAULT NULL';
        } else {
            $queries[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'product` MODIFY `hscode` VARCHAR(32) NULL DEFAULT NULL';
        }

        if (!hscodesColumnExists('origin')) {
            $queries[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'product` ADD `origin` VARCHAR(255) NULL DEFAULT NULL';
        } else {
            $queries[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'product` MODIFY `origin` VARCHAR(255) NULL DEFAULT NULL';
        }

        foreach ($queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }
}
