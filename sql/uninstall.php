<?php

return Db::getInstance()->execute('ALTER TABLE IF EXISTS `' . _DB_PREFIX_ . 'product DROP COLUMN hscode`');
