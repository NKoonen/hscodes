<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * The original class did not take the logo url right for the pdf the generate, here we fixed this.
 * @since 1.5
 */
abstract class HTMLTemplate extends HTMLTemplateCore
{

    /**
     * Override the parent getTemplate to also search for templates in this module
     *
     * @param $template_name
     *
     * @return string
     */
    protected function getTemplate($template_name)
    {
        $template = false;

        $theme_template = _PS_ALL_THEMES_DIR_ . $this->shop->theme->getName() . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . $template_name . '.tpl';
        
        // Allows other modules to hook in, but only take the last value
        $other_modules_template = Hook::exec('actionGetPdfTemplateDir', ['template_name' => $template_name], null, true);
        $other_modules_template = end($other_modules_template);

        $current_module_template = _PS_MODULE_DIR_ . 'hscodes' . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . $template_name . '.tpl';

        $default_template = rtrim(_PS_PDF_DIR_, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $template_name . '.tpl';

        if (file_exists($theme_template)) {
            $template = $theme_template;
        } elseif (file_exists($other_modules_template)) {
            $template = $other_modules_template;
        } elseif (file_exists($current_module_template)) {
            $template = $current_module_template;
        } elseif (file_exists($default_template)) {
            $template = $default_template;
        }

        return $template;
    }
}
