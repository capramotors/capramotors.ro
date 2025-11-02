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

namespace Iqit\IqitProductVariants\Repository;

use Iqit\IqitProductVariants\Entity\ProductVariant;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

class ProductVariantsRepository extends AbstractObjectModelRepository
{
    private $contextLangId;
    private $contextShopId;

    public function __construct(
        $contextLangId,
        $contextShopId,
    ) {
        $this->contextLangId = $contextLangId;
        $this->contextShopId = $contextShopId;
    }

    public function getByIdProduct(int $productId): array
    {
        $variantsArray = [];
        try {
            $productVariant = ProductVariant::getInstanceByProductId($productId);

            if ($productVariant->variants) {
                $variantsArray = explode(',', $productVariant->variants);
                array_unshift($variantsArray, $productId);
                $variantsArray = $this->getProductsInfoByIds($variantsArray, $this->contextLangId, $this->contextShopId);
            }
        } catch (\PrestaShopException $e) {
            throw new CoreException(sprintf(
                'Error occurred when fetching product variants for product #%d: %s',
                $productId,
                $e->getMessage()
            ));
        }
        return $variantsArray;
    }

    public function getByIdProductForBackend(int $productId): array
    {
        $variantsArray = [];
        try {
            $productVariant = ProductVariant::getInstanceByProductId($productId);

            if ($productVariant->variants) {
                $variantsArray = explode(',', $productVariant->variants);
                $variantsArray = $this->getProductsInfoByIds($variantsArray, $this->contextLangId, $this->contextShopId, false);
            }
        } catch (\PrestaShopException $e) {
            throw new CoreException(sprintf(
                'Error occurred when fetching product variants for product #%d: %s',
                $productId,
                $e->getMessage()
            ));
        }

        return $variantsArray;
    }

    public function getProductsInfoByIds(array $product_ids, int $id_lang, int $id_shop, bool $active = true): array
    {
        $idsList = implode(',', array_map('intval', $product_ids));
        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`link_rewrite`,
					pl.`meta_description`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`,
					image_shop.`id_image` id_image, il.`legend`, m.`name` as manufacturer_name, cl.`name` AS category_default, IFNULL(product_attribute_shop.id_product_attribute, 0) id_product_attribute,
					DATEDIFF(
						p.`date_add`,
						DATE_SUB(
							"' . date('Y-m-d') . ' 00:00:00",
							INTERVAL ' . (\Validate::isUnsignedInt(\Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? \Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY
						)
					) > 0 AS new
				FROM  `' . _DB_PREFIX_ . 'product` p 
				' . \Shop::addSqlAssociation('product', 'p') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
					ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $id_shop . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int) $id_lang . \Shop::addSqlRestrictionOnLang('pl') . '
				)
				LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (
					product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = ' . (int) $id_lang . \Shop::addSqlRestrictionOnLang('cl') . '
				)
				LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $id_shop . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (p.`id_manufacturer`= m.`id_manufacturer`)
				' . \Product::sqlStock('p', 0) . '
				WHERE p.id_product IN (' . $idsList . ')' .
            ($active ? ' AND product_shop.`active` = 1 AND product_shop.`visibility` != \'none\'' : '') . '
				ORDER BY FIELD(product_shop.id_product, ' . $idsList . ')';
        if (!$result = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
            return [];
        }
        foreach ($result as &$row) {
            $row['id_product_attribute'] = \Product::getDefaultAttribute((int) $row['id_product']);
        }

        return \Product::getProductsProperties($id_lang, $result);
    }
}
