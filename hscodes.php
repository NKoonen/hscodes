<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

use Symfony\Component\Form\Extension\Core\Type\TextType;

class Hscodes extends Module
{
    public function __construct()
    {
        $this->name = 'hscodes';
        $this->tab = 'shipping_logistics';
        $this->version = '1.3.1';
        $this->author = 'Inform-All';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('HS Codes');
        $this->description = $this->l('Fill in HS codes and country of origin for your products to make international sales easier.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall and delete your current HS codes?');

        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '9.99.99',
        ];
    }

    public function install()
    {
        include dirname(__FILE__) . '/sql/install.php';

        return installSql()
            && parent::install()
            && $this->registerHook([
                // PrestaShop 1.7/8 legacy product page fallback.
                'displayAdminProductsExtra',
                'displayAdminProductsMainStepLeftColumnMiddle',
                'actionProductUpdate',
                'actionAdminProductsControllerSaveAfter',

                // PrestaShop 8.1+/9 product page.
                'actionProductFormBuilderModifier',
                'actionAfterCreateProductFormHandler',
                'actionAfterUpdateProductFormHandler',

                // Back-office order pages.
                'displayAdminOrderTabContent',
                'displayAdminOrder',
            ]);
    }

    public function uninstall()
    {
        include dirname(__FILE__) . '/sql/uninstall.php';

        return uninstallSql() && parent::uninstall();
    }

    public function getContent()
    {
        $showMissingHsCodeProducts = (bool) Tools::getValue('displayMissingHsCodeProducts');

        $missingHsCodeProducts = [];

        if ($showMissingHsCodeProducts) {
            $missingHsCodeProducts = $this->getProductsWithoutHsCode();
        }

        $this->context->smarty->assign([
                                           'module_dir' => $this->_path,
                                           'show_missing_hscode_products' => $showMissingHsCodeProducts,
                                           'missing_hscode_products' => $missingHsCodeProducts,
                                           'missing_hscode_products_url' => $this->getConfigureUrl([
                                                                                                       'displayMissingHsCodeProducts' => 1,
                                                                                                   ]),
                                           'configure_url' => $this->getConfigureUrl(),
                                       ]);

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    public function hookActionProductFormBuilderModifier(array $params): void
    {
        if (empty($params['form_builder'])) {
            return;
        }

        $productId = !empty($params['id']) ? (int) $params['id'] : 0;
        $values = $this->getProductHsValues($productId);
        $formBuilder = $params['form_builder'];

        if (!$formBuilder->has('description')) {
            return;
        }

        $descriptionBuilder = $formBuilder->get('description');

        if (!$descriptionBuilder->has('hscode')) {
            $descriptionBuilder->add('hscode', TextType::class, [
                'label' => $this->l('HS Code'),
                'required' => false,
                'data' => $values['hscode'],
                'empty_data' => '',
                'attr' => [
                    'maxlength' => 32,
                    'placeholder' => $this->l('Example: 84713000'),
                ],
                'form_theme' => '@PrestaShop/Admin/TwigTemplateForm/prestashop_ui_kit_base.html.twig',
            ]);
        }

        if (!$descriptionBuilder->has('origin')) {
            $descriptionBuilder->add('origin', TextType::class, [
                'label' => $this->l('Country of origin'),
                'required' => false,
                'data' => $values['origin'],
                'empty_data' => '',
                'attr' => [
                    'maxlength' => 255,
                    'placeholder' => $this->l('Example: Netherlands'),
                ],
                'form_theme' => '@PrestaShop/Admin/TwigTemplateForm/prestashop_ui_kit_base.html.twig',
            ]);
        }
    }

    public function hookActionAfterCreateProductFormHandler(array $params): void
    {
        $this->saveProductFormHandlerData($params);
    }

    public function hookActionAfterUpdateProductFormHandler(array $params): void
    {
        $this->saveProductFormHandlerData($params);
    }

    public function hookActionAdminProductsControllerSaveAfter(array $params): void
    {
        $this->saveLegacyProductPostData();
    }

    public function hookActionProductUpdate(array $params): void
    {
        $this->saveLegacyProductPostData($params);
    }

    public function hookDisplayAdminProductsMainStepLeftColumnMiddle(array $params)
    {
        return $this->renderProductHsCodeFields($params);
    }

    public function hookDisplayAdminProductsExtra(array $params)
    {
        return $this->renderProductHsCodeFields($params);
    }

    public function hookDisplayAdminOrderTabContent(array $params)
    {
        return $this->renderAdminOrderProducts($params);
    }

    public function hookDisplayAdminOrder(array $params)
    {
        return $this->renderAdminOrderProducts($params);
    }

    private function renderProductHsCodeFields(array $params)
    {
        $idProduct = $this->resolveProductId($params);

        if ($idProduct <= 0) {
            return '';
        }

        $values = $this->getProductHsValues($idProduct);

        $this->context->smarty->assign([
            'hscode' => $values['hscode'],
            'origin' => $values['origin'],
        ]);

        return $this->display(__FILE__, 'views/templates/admin/create.tpl');
    }

    private function renderAdminOrderProducts(array $params)
    {
        if (empty($params['id_order'])) {
            return '';
        }

        $order = new Order((int) $params['id_order']);

        if (!Validate::isLoadedObject($order)) {
            return '';
        }

        $products = $this->enrichOrderProductsWithHsValues($order->getProducts());

        if (isset($this->context->smarty)) {
            $this->context->smarty->assign(['prods' => $products]);
        }

        if (method_exists($this, 'get')) {
            try {
                return $this->get('twig')->render('@Modules/hscodes/views/templates/admin/bo-order.html.twig', [
                    'prods' => $products,
                ]);
            } catch (Exception $e) {
                // Fall back to Smarty on older installations or when Twig is not available.
            }
        }

        return $this->display(__FILE__, 'views/templates/admin/bo-order.tpl');
    }

    private function saveProductFormHandlerData(array $params): void
    {
        $productId = !empty($params['id']) ? (int) $params['id'] : 0;

        if ($productId <= 0 && !empty($params['form_data']['id_product'])) {
            $productId = (int) $params['form_data']['id_product'];
        }

        if ($productId <= 0) {
            return;
        }

        $formData = !empty($params['form_data']) && is_array($params['form_data']) ? $params['form_data'] : [];
        $description = !empty($formData['description']) && is_array($formData['description']) ? $formData['description'] : [];

        $hscode = $description['hscode'] ?? ($formData['hscode'] ?? null);
        $origin = $description['origin'] ?? ($formData['origin'] ?? null);

        if ($hscode !== null || $origin !== null) {
            $this->saveProductHsValues($productId, $hscode, $origin);
        }
    }

    private function saveLegacyProductPostData(array $params = []): void
    {
        $productId = $this->resolveProductId($params);

        if ($productId <= 0) {
            return;
        }

        $hscode = Tools::getValue('hscode', Tools::getValue('hs_code', null));
        $origin = Tools::getValue('origin', null);

        if ($hscode !== null || $origin !== null) {
            $this->saveProductHsValues($productId, $hscode, $origin);
        }
    }

    private function resolveProductId(array $params = []): int
    {
        foreach (['id_product', 'id'] as $key) {
            if (!empty($params[$key])) {
                return (int) $params[$key];
            }
        }

        foreach (['id_product', 'id'] as $key) {
            $value = Tools::getValue($key);
            if ($value) {
                return (int) $value;
            }
        }

        $form = Tools::getValue('form');
        if (is_array($form) && !empty($form['id_product'])) {
            return (int) $form['id_product'];
        }

        return 0;
    }

    private function getProductHsValues(int $productId): array
    {
        if ($productId <= 0) {
            return ['hscode' => '', 'origin' => ''];
        }

        $row = Db::getInstance()->getRow(
            'SELECT `hscode`, `origin` FROM `' . _DB_PREFIX_ . 'product` WHERE `id_product` = ' . (int) $productId
        );

        return [
            'hscode' => isset($row['hscode']) ? (string) $row['hscode'] : '',
            'origin' => isset($row['origin']) ? (string) $row['origin'] : '',
        ];
    }

    private function saveProductHsValues(int $productId, $hscode, $origin): void
    {
        if ($productId <= 0) {
            return;
        }

        $updates = [];

        if ($hscode !== null) {
            $updates[] = '`hscode` = \'' . pSQL(trim((string) $hscode)) . '\'';
        }

        if ($origin !== null) {
            $updates[] = '`origin` = \'' . pSQL(trim((string) $origin)) . '\'';
        }

        if (!$updates) {
            return;
        }

        Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'product` SET ' . implode(', ', $updates) . ' WHERE `id_product` = ' . (int) $productId
        );
    }

    private function enrichOrderProductsWithHsValues(array $products): array
    {
        $productIds = [];

        foreach ($products as $product) {
            if (!empty($product['product_id'])) {
                $productIds[] = (int) $product['product_id'];
            } elseif (!empty($product['id_product'])) {
                $productIds[] = (int) $product['id_product'];
            }
        }

        $productIds = array_values(array_unique(array_filter($productIds)));

        if (!$productIds) {
            return $products;
        }

        $rows = Db::getInstance()->executeS(
            'SELECT `id_product`, `hscode`, `origin` FROM `' . _DB_PREFIX_ . 'product` WHERE `id_product` IN (' . implode(',', $productIds) . ')'
        );

        $byProductId = [];
        foreach ($rows as $row) {
            $byProductId[(int) $row['id_product']] = [
                'hscode' => (string) $row['hscode'],
                'origin' => (string) $row['origin'],
            ];
        }

        foreach ($products as &$product) {
            $productId = !empty($product['product_id']) ? (int) $product['product_id'] : (!empty($product['id_product']) ? (int) $product['id_product'] : 0);
            if ($productId > 0 && isset($byProductId[$productId])) {
                $product['hscode'] = $byProductId[$productId]['hscode'];
                $product['origin'] = $byProductId[$productId]['origin'];
            } else {
                $product['hscode'] = $product['hscode'] ?? '';
                $product['origin'] = $product['origin'] ?? '';
            }
        }
        unset($product);

        return $products;
    }

    private function getConfigureUrl(array $extraParams = []): string
    {
        return $this->context->link->getAdminLink('AdminModules', true, [], array_merge([
                                                                                            'configure' => $this->name,
                                                                                            'module_name' => $this->name,
                                                                                        ], $extraParams));
    }

    private function getProductsWithoutHsCode(): array
    {
        $idLang = (int) $this->context->language->id;
        $idShop = (int) $this->context->shop->id;

        $sql = '
        SELECT
            p.`id_product`,
            p.`reference`,
            p.`active`,
            p.`hscode`,
            pl.`name`
        FROM `' . _DB_PREFIX_ . 'product` p
        LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
            ON pl.`id_product` = p.`id_product`
            AND pl.`id_lang` = ' . (int) $idLang . '
            AND pl.`id_shop` = ' . (int) $idShop . '
        WHERE p.`hscode` = 0
            OR TRIM(p.`hscode`) = \'\'
        ORDER BY p.`id_product` ASC
    ';

        $rows = Db::getInstance()->executeS($sql);

        if (!is_array($rows)) {
            return [];
        }

        foreach ($rows as &$row) {
            $row['edit_url'] = $this->getProductEditUrl((int) $row['id_product']);
        }

        unset($row);

        return $rows;
    }

    private function getProductEditUrl(int $productId): string
    {
        if ($productId <= 0) {
            return '#';
        }

        if (method_exists($this, 'get')) {
            try {
                return $this->get('router')->generate('admin_products_edit', [
                    'productId' => $productId,
                ]);
            } catch (Exception $e) {
                // Fall back to legacy URL below.
            }
        }

        return $this->context->link->getAdminLink('AdminProducts', true, [], [
            'id_product' => $productId,
            'updateproduct' => 1,
        ]);
    }
}
