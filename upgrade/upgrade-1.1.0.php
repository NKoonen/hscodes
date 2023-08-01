<?php
function upgrade_module_1_1_0( $module )
{
	Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'product` ADD `origin` varchar(255)');
	$module->registerHook( 'displayAdminOrderTabContent' );
	return true;
}