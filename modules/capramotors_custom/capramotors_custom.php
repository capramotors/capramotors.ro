<?php
/**
 * CapraMotors Custom Module
 *
 * Custom module for CapraMotors theme to register additional assets
 *
 * @author    CapraMotors
 * @copyright CapraMotors
 * @license   Proprietary
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use ProductControllerCore;

class Capramotors_Custom extends Module
{
    public function __construct()
    {
        $this->name = 'capramotors_custom';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'CapraMotors';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => _PS_VERSION_,
        ];

        parent::__construct();

        $this->displayName = $this->trans('CapraMotors Custom', [], 'Modules.Capramotorscustom.Admin');
        $this->description = $this->trans('Custom module for CapraMotors theme assets', [], 'Modules.Capramotorscustom.Admin');
    }

    /**
     * Install the module
     *
     * @return bool
     */
    public function install()
    {
        return parent::install()
            && $this->registerHook('actionFrontControllerSetMedia')
            && $this->registerHook('displayFooterProduct');
    }

    /**
     * Reset the module - re-register hooks
     *
     * @return bool
     */
    public function reset()
    {
        return $this->uninstall(false) && $this->install();
    }

    /**
     * Uninstall the module
     *
     * @return bool
     */
    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * Hook to register JavaScript and CSS files
     *
     * @return void
     */
    public function hookActionFrontControllerSetMedia()
    {
        // Register jQuery UI Touch Punch after jQuery UI plugins but before custom.js
        // Priority 800 ensures it loads after jQuery UI (50-200) but before custom.js (1000)
        // Position 'bottom' places it at the bottom of the page between jQuery UI and custom.js
        $this->context->controller->registerJavascript(
            'jquery-ui-touch-punch',
            'modules/' . $this->name . '/views/js/jquery-ui-touch-punch.js',
            [
                'position' => 'bottom',
                'priority' => 800,
            ]
        );
    }

    /**
     * Get content for module configuration page
     * This also ensures hooks are registered
     */
    public function getContent()
    {
        // Ensure hooks are registered (in case they weren't during install)
        $this->registerHook('displayFooterProduct');

        return $this->displayConfirmation($this->trans('Module configured successfully.', [], 'Admin.Notifications.Success'));
    }

    /**
     * Display contact form in product footer on product page
     *
     * @param array $params Contains 'product' and 'category' arrays
     * @return string HTML output
     */
    public function hookDisplayFooterProduct($params)
    {
        // Only show on product pages
        if ($this->context->controller->php_self !== 'product') {
            return '';
        }

        // Get contactform module instance
        $contactformModule = Module::getInstanceByName('contactform');

        if (!$contactformModule || !$contactformModule->active) {
            return '';
        }

        // Check if contactform implements WidgetInterface
        if (!($contactformModule instanceof PrestaShop\PrestaShop\Core\Module\WidgetInterface)) {
            return '';
        }

        // Get widget variables from contactform
        $widgetVars = $contactformModule->getWidgetVariables('displayAfterProductWrapper', []);

        // Add form_recipient for product page (Service contact = 3)
        $widgetVars['form_recipient'] = 3;

        // Add product information if available (for PDP)
        // The product is passed as $params['product'] from the template hook
        // It's a presented product array from getTemplateVarProduct()
        if (isset($params['product'])) {
            $product = $params['product'];

            // Debug: Check what we're getting
            // Product can be array or might need to get from context
            if (is_array($product) && isset($product['name'])) {
                $widgetVars['product_name'] = $product['name'];
                $widgetVars['is_product_page'] = true;
            } else {
                // Fallback: try to get product from context controller
                if ($this->context->controller instanceof ProductControllerCore) {
                    $controllerProduct = $this->context->controller->getTemplateVarProduct();
                    if (isset($controllerProduct['name'])) {
                        $widgetVars['product_name'] = $controllerProduct['name'];
                        $widgetVars['is_product_page'] = true;
                    }
                }
            }
        }

        // Assign variables to smarty (both in widgetVars and directly for template include)
        $this->context->smarty->assign($widgetVars);
        // Also assign directly so they're available when including the contactform template
        if (isset($widgetVars['product_name'])) {
            $this->context->smarty->assign('product_name', $widgetVars['product_name']);
            $this->context->smarty->assign('is_product_page', true);
        }

        // Render using the elementor template (which supports form_recipient)
        return $this->display(__FILE__, 'views/templates/hook/product-contactform.tpl');
    }
}

