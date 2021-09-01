<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Hscodes extends Module
{
    public function __construct()
    {
        $this->name = 'hscodes';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'Inform-All';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('HS Codes');
        $this->description = $this->l('Fill in HS codes for your products to make international sales easier.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall and delete your current HS codes?');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        include(dirname(__FILE__) . '/sql/install.php');

        return parent::install() &&
            $this->registerHook('displayAdminProductsMainStepLeftColumnMiddle')
            && $this->registerHook('actionProductUpdate');
    }

    public function uninstall()
    {
        include(dirname(__FILE__) . '/sql/uninstall.php');
        return parent::uninstall();
    }

    public function getContent()
    {
        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');

    }

    public function hookDisplayAdminProductsMainStepLeftColumnMiddle($params)
    {
        $id = $params['id_product'];
        if (empty($id)) {
            return '';
        }
        $product = new Product($id);
        $this->context->smarty->assign('hscode', $product->hscode);
        return $this->display(__FILE__, 'views/templates/admin/create.tpl');
    }

    public function hookActionProductUpdate($params)
    {
        $id = Tools::getValue('id_product');
        $product = new Product($id);
        if((int)Tools::getValue('hs_code') != $product->hscode) {
            $product->hscode = (int)Tools::getValue('hs_code');
            $product->save();
        }
    }
}
