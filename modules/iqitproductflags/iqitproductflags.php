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

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use Iqit\IqitProductFlags\Database\IqitProductFlagInstaller;
use Iqit\IqitProductFlags\Presenter\FlagPresenter;
use Iqit\IqitProductFlags\Repository\FlagRepository;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

class IqitProductFlags extends Module
{
    const TRANSLATION_DOMAIN_ADMIN = 'Modules.Iqitproductflags.Config';

    public $multistoreCompatibility = self::MULTISTORE_COMPATIBILITY_YES;

    public function __construct()
    {
        $this->name = 'iqitproductflags';
        $this->author = 'iqit-commerce.com';
        $this->version = '1.0.0';
        $this->need_instance = 0;

        $tabNames = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tabNames[$lang['locale']] = $this->trans('Product flags', [], self::TRANSLATION_DOMAIN_ADMIN, $lang['locale']);
        }
        $this->tabs = [
            [
                'route_name' => 'iqitproductflags',
                'class_name' => 'AdminIqitProductFlags',
                'visible' => true,
                'name' => $tabNames,
                'parent_class_name' => 'AdminCatalog',
                'wording' => 'Product flags',
                'wording_domain' => self::TRANSLATION_DOMAIN_ADMIN,
            ],
        ];

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('IQITPRODUCTFLAGS', [], self::TRANSLATION_DOMAIN_ADMIN);
        $this->description = $this->trans('Add additional product flags', [], self::TRANSLATION_DOMAIN_ADMIN);
    }

    /**
     * Inform prestashop abut new translation system
     *
     * @return bool
     */
    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function install(): bool
    {
        return $this->getInstaller()->createTables()
            && parent::install()
            && $this->registerHook('displayProductCampagains');
    }

    /**
     * @return bool
     */
    public function uninstall(): bool
    {
        return $this->getInstaller()->dropTables() && parent::uninstall();
    }

    public function getContent(): void
    {
        $route = SymfonyContainer::getInstance()->get('router')->generate('iqitproductflags');
        Tools::redirectAdmin($route);
    }

    /**
     * Gets the IqitProductFlagInstaller from service container if possible (at uninstall),
     * otherwise instantiate class directly (at install)
     *
     * @return IqitProductFlagInstaller
     */
    private function getInstaller(): IqitProductFlagInstaller
    {
        try {
            $installer = $this->get('iqit.iqitproductflags.iqit_product_flag_installer');
        } catch (Exception $e) {
            $installer = null;
        }

        if (empty($installer)) {
            $installer = new IqitProductFlagInstaller(
                $this->get('doctrine.dbal.default_connection'),
                $this->getContainer()->getParameter('database_prefix')
            );
        }

        return $installer;
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayProductCampagains($params): string
    {
        if (empty($params['product']->id)) {
            return '';
        }
        $templates = [
            'default' => [
                'file' => 'module:iqitproductflags/views/templates/hook/small.tpl',
                'hooks' => [0, 2],
            ],
            'miniature' => [
                'file' => 'module:iqitproductflags/views/templates/hook/miniature.tpl',
                'hooks' => [0, 1, 4],
            ],
            'highlighted-product' => [
                'file' => 'module:iqitproductflags/views/templates/hook/highlight.tpl',
                'hooks' => [1, 3],
            ],
        ];

        $position = $params['position'] ?? 'default';
        $config = $templates[$position] ?? $templates['default'];

        $idProduct = $params['product']->id;
        $productCategories = Product::getProductCategories($idProduct);

        /** @var FlagRepository $repository */
        $repository = $this->get('iqit.iqitproductflags.repository.flags_repository');
        $langId = (int) $this->context->language->id;
        $shopId = (int) $this->context->shop->id;
        $flags = $repository->findByProduct($langId, $shopId, $productCategories, $config['hooks']);

        $presenter = new FlagPresenter();
        $flagsForSmarty = $presenter->presentCollection($flags);

        $this->smarty->assign([
            'flags' => $flagsForSmarty,
            'params' => $params,
        ]);

        return $this->fetch($config['file']);
    }
}
