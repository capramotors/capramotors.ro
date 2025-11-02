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

namespace Iqit\IqitProductFlags\Form;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Handles configuration data for demo multistore configuration options.
 */
final class IqitProductFlagDataConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var array<int, string>
     */
    private const CONFIGURATION_FIELDS = ['color', 'italic', 'bold'];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): array
    {
        $return = [];
        $shopConstraint = $this->getShopConstraint();

        //$return['color'] = $this->configuration->get('PS_DEMO_MULTISTORE_COLOR', null, $shopConstraint);

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration): array
    {
        $shopConstraint = $this->getShopConstraint();
       // $this->updateConfigurationValue('PS_DEMO_MULTISTORE_COLOR', 'color', $configuration, $shopConstraint);

        return [];
    }

    /**
     * @param array $configuration
     *
     * @return bool Returns true if no exception are thrown
     */
    public function validateConfiguration(array $configuration): bool
    {
        return true;
    }

    protected function buildResolver(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined(self::CONFIGURATION_FIELDS);
       // $resolver->setAllowedTypes('color', 'string');


        return $resolver;
    }
}
