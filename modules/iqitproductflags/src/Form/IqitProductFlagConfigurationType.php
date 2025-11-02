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

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\FormBuilderInterface;

class IqitProductFlagConfigurationType extends CommonAbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**=
        $builder
        ->add('color', ColorType::class, [
            'attr' => ['class' => 'col-md-4 col-lg-2 p-1 h-25 w-50'],
            'required' => false,
            'label' => 'Color',
            'multistore_configuration_key' => 'PS_DEMO_MULTISTORE_COLOR',
            ])
        ->add(
            'italic',
            SwitchType::class,
            [
                'label' => 'Italic',
                'multistore_configuration_key' => 'PS_DEMO_MULTISTORE_ITALIC',
            ]
        )
        ->add(
            'bold',
            SwitchType::class,
            [
                'label' => 'Bold',
                'multistore_configuration_key' => 'PS_DEMO_MULTISTORE_BOLD',
            ]
        );
             */
    }

    /**
     * {@inheritdoc}
     *
     * @see MultistoreConfigurationTypeExtension
     */
    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }
}
