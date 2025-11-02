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

namespace Iqit\IqitProductVariants\Presenter;

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

class ProductVariantsPresenter
{
    private $context;

    public function __construct(
        $context,
    ) {
        $this->context = $context->getContext();
    }

    public function present(array $products): array
    {
        if (empty($products)) {
            return [];
        }

        $presenterFactory = new \ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();

        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );


        foreach ($products as &$product) {
            $product = $presenter->present(
                $presentationSettings,
                \Product::getProductProperties($this->context->language->id, $product, $this->context),
                $this->context->language
            );
        }
        unset($product);


        return $products;
    }

    public function presentBackend(array $products): array
    {

        if (empty($products)) {
            return [];
        }

        $presenterFactory = new \ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();

        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );


        foreach ($products as &$product) {
            $product = $presenter->present(
                $presentationSettings,
                \Product::getProductProperties($this->context->language->id, $product, $this->context),
                $this->context->language
            );
        }
        unset($product);


        $selectedVariants = [];
        foreach ($products as $product) {
            $selectedVariants[] = [
                'id' => $product['id_product'],
                'name' => $product['name'] . ' (ref: ' . $product['reference'] . ')',
                'image' => $product['cover']['medium']['url'],
            ];
        }

        return $selectedVariants;
    }
}
