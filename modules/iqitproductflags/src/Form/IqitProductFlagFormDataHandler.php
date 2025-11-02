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

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Iqit\IqitProductFlags\Entity\IqitProductFlag;
use Iqit\IqitProductFlags\Entity\IqitProductFlagLang;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Entity\Shop;

class IqitProductFlagFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(private EntityManagerInterface $entityManager,   private LangRepository $langRepository){
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): int
    {
        $entity = new IqitProductFlag();
        $this->populateEntityWithData($entity, $data);
        $this->addAssociatedCategories($entity, $data['categories'] ?? null);

        $entity->setPosition(
            (int) \Db::getInstance()->getValue('SELECT MAX(position) AS max FROM ' . _DB_PREFIX_ . 'iqit_product_flag') + 1 ?: 1
        );

        $this->addAssociatedShops($entity, $data['shop_association'] ?? null);

        foreach ($data['title'] as $langId => $langContent) {
            $this->addOrUpdateEntityLang($entity, $langId, $langContent, $data);
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data): int
    {
        $entity = $this->entityManager->getRepository(IqitProductFlag::class)->find($id);
        $entity->clearCategories();
        $this->entityManager->flush();
        $this->addAssociatedCategories($entity, $data['categories'] ?? null);
        $this->populateEntityWithData($entity, $data);

        foreach ($data['title'] as $langId => $langContent) {
            $this->addOrUpdateEntityLang($entity, $langId, $langContent, $data, true);
        }

        if (!is_array($data['shop_association'])) {
            $data['shop_association'] = [$data['shop_association']];
        }
        $this->addAssociatedShops($entity, $data['shop_association']);

        $this->entityManager->flush();

        return $entity->getId();
    }

    /**
     * Populate the entity with common data.
     */
    private function populateEntityWithData(IqitProductFlag $entity, array $data): void
    {
        $entity->setEnable($data['enable']);
        $entity->setFromDate($this->createDateTimeOrNull($data['from_date'] ?? null));
        $entity->setToDate($this->createDateTimeOrNull($data['to_date'] ?? null));
        $entity->setHook($data['hook']);

        $config = [];
        foreach ($data as $key => $value) {
            if (strpos($key, 'config_') === 0) {
                $newKey = substr($key, strlen('config_'));
                $config[$newKey] = $value;
            }
        }

        $entity->setConfig($config);
    }

    /**
     * Create a DateTime object or return null on failure.
     */
    private function createDateTimeOrNull(?string $date): ?\DateTime
    {
        try {
            return $date ? new \DateTime($date) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Add or update a language entity.
     *
     * @return void
     */
    private function addOrUpdateEntityLang(
        IqitProductFlag $entity,
        int $langId,
        $langContent,
        array $data,
        bool $update = false,
    ): void {
        if ($update) {
            $entityLang = $entity->getLangByLangId($langId);
            if (null === $entityLang) {
                return;
            }
        } else {
            $entityLang = new IqitProductFlagLang();
            // @phpstan-ignore-next-line
            $lang = $this->langRepository->findOneById($langId);
            $entityLang->setLang($lang);
            $entity->addLang($entityLang);
        }

        $entityLang
            ->setTitle($langContent)
            ->setDescription($data['description'][$langId] ?? '')
            ->setLink($data['link'][$langId] ?? '');
    }

    private function addAssociatedShops(IqitProductFlag &$entity, ?array $shopIdList = null): void
    {
        $entity->clearShops();

        if (empty($shopIdList)) {
            return;
        }

        foreach ($shopIdList as $shopId) {
            $shop = $this->entityManager->getRepository(Shop::class)->find($shopId);
            $entity->addShop($shop);
        }
    }

    private function addAssociatedCategories(IqitProductFlag &$entity, ?array $categoriesIdList = null): void
    {
        if (empty($categoriesIdList)) {
            return;
        }

        foreach ($categoriesIdList as $categoryId) {
            $entity->addCategory((int) $categoryId);
        }
    }
}
