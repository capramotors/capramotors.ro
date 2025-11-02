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

namespace Iqit\IqitProductVariants\Form\Modifier;

use PrestaShopBundle\Form\Admin\Type\EntitySearchInputType;
use PrestaShopBundle\Form\FormBuilderModifier;
use PrestaShopBundle\Translation\TranslatorInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

final class ProductFormModifier
{
    /**
     * @var FormBuilderModifier
     */
    private FormBuilderModifier $formBuilderModifier;
    private TranslatorInterface $translator;

    /**
     * @var RouterInterface
     */
    private RouterInterface $router;

    /**
     * @var string
     */
    private string $employeeIsoCode;

    /**
     * @param FormBuilderModifier $formBuilderModifier
     */
    public function __construct(
        FormBuilderModifier $formBuilderModifier,
        TranslatorInterface $translator,
        RouterInterface $router,
        string $employeeIsoCode,
    ) {
        $this->formBuilderModifier = $formBuilderModifier;
        $this->translator = $translator;
        $this->router = $router;
        $this->employeeIsoCode = $employeeIsoCode;
    }

    /**
     * @param int $productId
     * @param FormBuilderInterface $productFormBuilder
     */
    public function modify(
        int $productId,
        FormBuilderInterface $productFormBuilder,
        array $data,
    ): void {
        $descTabFormBuilder = $productFormBuilder->get('description');
        $this->formBuilderModifier->addAfter(
            $descTabFormBuilder, // the tab
            'related_products', // the input/form from which to insert after/before
            'variants', // your field name
            EntitySearchInputType::class,
            [
                'label' => $this->translator->trans('Variants', [], 'Modules.Iqitproductvariants.Admin'),
                'label_tag_name' => 'h3',
                'entry_options' => [
                    'block_prefix' => 'related_product',
                ],
                'remote_url' => $this->router->generate('admin_products_search_products_for_association', [
                    'languageCode' => $this->employeeIsoCode,
                    'query' => '__QUERY__',
                ]),
                'min_length' => 3,
                'filtered_identities' => $productId > 0 ? [$productId] : [],
                'placeholder' => $this->translator->trans('Search product', [], 'Modules.Iqitproductvariants.Admin'),
                'data' => $data,
            ]
        );
    }
}
