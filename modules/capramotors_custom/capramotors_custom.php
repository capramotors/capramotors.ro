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
            && $this->registerHook('actionFrontControllerSetMedia');
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
        // Register jQuery UI Touch Punch after jQuery plugins but before custom.js
        // Priority 800 ensures it loads after jQuery plugins (50-200) but before custom.js (1000)
        // Position 'head' places it in the head element as requested
        $this->context->controller->registerJavascript(
            'jquery-ui-touch-punch',
            'modules/' . $this->name . '/views/js/jquery-ui-touch-punch.js',
            [
                'position' => 'head',
                'priority' => 800,
            ]
        );
    }
}

