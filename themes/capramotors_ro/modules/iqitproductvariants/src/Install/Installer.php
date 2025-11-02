<?php

/**
 * Copyright since 2025 iqit-commerce.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Envato Regular License,
 * which is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at the following URL:
 * https://themeforest.net/licenses/terms/regular
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@iqit-commerce.com so we can send you a copy immediately.
 *
 * @author    iqit-commerce.com <support@iqit-commerce.com>
 * @copyright Since 2025 iqit-commerce.com
 * @license   Envato Regular License
 */

declare(strict_types=1);

namespace Iqit\IqitProductVariants\Install;

use Module;

class Installer
{
    /**
     * Module's installation entry point.
     *
     * @param \Module $module
     *
     * @return bool
     */
    public function install(\Module $module): bool
    {
        if (!$this->registerHooks($module)) {
            return false;
        }
        if (!$this->executeSqlFromFile($module->getLocalPath() . 'src/Install/install.sql')) {
            return false;
        }

        return true;
    }

    /**
     * @param \Module $module
     *
     * @return bool
     */
    public function uninstall(\Module $module): bool
    {
        return $this->executeSqlFromFile($module->getLocalPath() . 'src/Install/uninstall.sql');
    }

    /**
     * Register hooks for the module.
     *
     * @see https://devdocs.prestashop.com/8/modules/concepts/hooks/
     *
     * @param \Module $module
     *
     * @return bool
     */
    private function registerHooks(\Module $module): bool
    {
        $hooks = [
            'actionProductFormBuilderModifier',
            'actionAfterUpdateProductFormHandler',
            'actionObjectProductDeleteAfter',
            'displayBackOfficeHeader',
            'actionProductAdd',
            'displayProductVariants',
            'actionFrontControllerSetMedia',
        ];

        return (bool) $module->registerHook($hooks);
    }

    /**
     * @param string $filepath
     *
     * @return bool
     */
    private function executeSqlFromFile(string $filepath): bool
    {
        if (!file_exists($filepath)) {
            return true;
        }

        $sql = \Tools::file_get_contents($filepath);

        if (!$sql) {
            return false;
        }

        $sql = str_replace(['_DB_PREFIX_', '_MYSQL_ENGINE_'], [_DB_PREFIX_, _MYSQL_ENGINE_], $sql);

        return (bool) \Db::getInstance()->execute($sql);
    }
}
