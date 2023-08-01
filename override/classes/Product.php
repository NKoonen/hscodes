<?php

class Product extends ProductCore
{
    /** @var string hscode */
    public $hscode;

	/** @var string origin */
	public $origin;

    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, \Context $context = null)
    {
        self::$definition['fields']['hscode'] = ['type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 10];
	    self::$definition['fields']['origin'] = ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255];
        parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
    }
}