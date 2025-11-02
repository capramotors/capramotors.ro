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

namespace Iqit\IqitProductVariants\Form\Product;

use Iqit\IqitProductVariants\Entity\ProductVariant;
use Iqit\IqitProductVariants\Factory\ProductVariantsFactory;
use Iqit\IqitProductVariants\Form\FormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException;

final class ProductFormDataHandler implements FormDataHandlerInterface
{
    public function save(array $data): bool
    {
        $idProduct = (int) $data['id_product'];
        $this->clearVariants($idProduct);

        if (!isset($data['variants']) || empty($data['variants'])) {
            return true;
        }

        $variantProductsIds = $data['variants'];
        $variantProductsIds[] = $idProduct;

        foreach ($variantProductsIds as $index => $variantsProduct) {
            $variantProductsIdsNew = $variantProductsIds;
            unset($variantProductsIdsNew[$index]);
            $this->saveVariant($variantsProduct, $variantProductsIdsNew);
        }

        return true;
    }

    public function getData(array $params): array
    {
        $productVariant = ProductVariantsFactory::create(
            (int) $params['id_product'],
            $params['id_lang'] ?? null,
            $params['id_shop'] ?? null
        );

        return [
            'id' => $productVariant->id,
            'id_product' => $productVariant->id_product,
            'variants' => ['data' => array_filter(explode(',', $productVariant->variants))],
        ];
    }

    public function duplicate(array $data): bool
    {
        $idProduct = (int) $data['id_product_old'];
        $idProductNew = (int) $data['id_product'];
        $productVariant = ProductVariantsFactory::create(
            $idProduct
        );

        $this->clearVariants($idProduct);

        $variantProductsIds = [];
        foreach (array_filter(explode(',', $productVariant->variants)) as $variantsProduct) {
            $variantProductsIds[$variantsProduct] = $variantsProduct;
        }

        $variantProductsIds[$idProduct] = $idProduct;
        $variantProductsIds[$idProductNew] = $idProductNew;

        foreach ($variantProductsIds as $variantsProduct) {
            $variantProductsIdsNew = $variantProductsIds;
            unset($variantProductsIdsNew[$variantsProduct]);
            $this->saveVariant($variantsProduct, $variantProductsIdsNew);
        }

        return true;
    }


    public function delete(array $data): bool
    {
        $idProduct = (int) $data['id_product'];
        $productVariant = ProductVariantsFactory::create($idProduct);

        $this->clearVariants($idProduct);

        // Tworzymy tablicę z wariantami, konwertując wartości na int
        $variantProductsIds = array_map('intval', array_filter(explode(',', $productVariant->variants)));

        foreach ($variantProductsIds as $variantsProduct) {
            $variantProductsIdsNew = $variantProductsIds;
            unset($variantProductsIdsNew[$variantsProduct]); // Klucz jako int

            $this->saveVariant($variantsProduct, $variantProductsIdsNew);
        }

        return true;
    }


    private function saveVariant(int $idProduct, array $variantProductsIds): bool
    {
        $productVariant = ProductVariantsFactory::create($idProduct);
        $productVariant->id_product = $idProduct;
        $productVariant->variants = implode(',', $variantProductsIds);

        try {
            if ($productVariant->save()) {
                return true;
            }
        } catch (\Exception $e) {
            throw new ModuleErrorException($e->getMessage());
        }
        return false;
    }

    private function removeVariant(ProductVariant $productVariant): bool
    {
        try {
            if ($productVariant->delete()) {
                return true;
            }
        } catch (\Exception $e) {
            throw new ModuleErrorException($e->getMessage());
        }

        return false;
    }

    private function clearVariants(int $idProduct): bool
    {
        $productVariant = ProductVariantsFactory::create($idProduct);
        $this->removeVariant($productVariant);

        $idsVariantsToRemove = array_filter(explode(',', $productVariant->variants));

        foreach ($idsVariantsToRemove as $idVariantProduct) {
            $productVariantLoop = ProductVariantsFactory::create((int) $idVariantProduct);
            $this->removeVariant($productVariantLoop);
        }

        return true;
    }
}
