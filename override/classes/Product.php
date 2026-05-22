<?php

class Product extends ProductCore
{
    /** @var string|null */
    public $hscode;

    /** @var string|null */
    public $origin;

    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, \Context $context = null)
    {
        self::$definition['fields']['hscode'] = [
            'type' => self::TYPE_STRING,
            'validate' => 'isGenericName',
            'size' => 32,
            'required' => false,
        ];

        self::$definition['fields']['origin'] = [
            'type' => self::TYPE_STRING,
            'validate' => 'isGenericName',
            'size' => 255,
            'required' => false,
        ];

        parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
    }
}
