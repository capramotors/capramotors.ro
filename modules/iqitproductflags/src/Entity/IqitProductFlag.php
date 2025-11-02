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

namespace Iqit\IqitProductFlags\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Entity\Shop;

/**
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="Iqit\IqitProductFlags\Repository\FlagRepository")
 *
 * @ORM\HasLifecycleCallbacks
 */
class IqitProductFlag
{
    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_iqit_product_flag", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @phpstan-ignore-next-line
     */
    private int $id;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private int $position;

    /**
     * @var int
     *
     * @ORM\Column(name="hook", type="integer")
     */
    private int $hook;

    /**
     * array|null
     *
     * @ORM\Column(name="config", type="text", nullable=true)
     */
    private $config;

    /**
     * @ORM\OneToMany(targetEntity="Iqit\IqitProductFlags\Entity\IqitProductFlagLang", cascade={"persist", "remove"}, mappedBy="entity")
     */
    private $entityLangs;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="from_date", type="datetime")
     */
    private $fromDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="to_date", type="datetime")
     */
    private $toDate;

    /**
     * @var bool
     *
     * @ORM\Column(name="enable", type="boolean")
     */
    private $enable;

    /**
     * @ORM\ManyToMany(targetEntity="PrestaShopBundle\Entity\Shop", cascade={"persist"})
     *
     * @ORM\JoinTable(
     *      joinColumns={@ORM\JoinColumn(name="id_iqit_product_flag", referencedColumnName="id_iqit_product_flag")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_shop", referencedColumnName="id_shop", onDelete="CASCADE")}
     * )
     */
    private Collection $shops;

    /**
     * @ORM\OneToMany(targetEntity="Iqit\IqitProductFlags\Entity\IqitProductFlagCategory", mappedBy="flag", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $categories;

    public function __construct()
    {
        $this->shops = new ArrayCollection();
        $this->entityLangs = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection
     */
    public function getLangs()
    {
        return $this->entityLangs;
    }

    /**
     * @param int $langId
     *
     * @return IqitProductFlagLang|null
     */
    public function getLangByLangId(int $langId)
    {
        foreach ($this->entityLangs as $flagLang) {
            if ($langId === $flagLang->getLang()->getId()) {
                return $flagLang;
            }
        }

        return null;
    }

    /**
     * @param IqitProductFlagLang $flagLang
     *
     * @return $this
     */
    public function addLang(IqitProductFlagLang $flagLang): self
    {
        $flagLang->setFlag($this);
        $this->entityLangs->add($flagLang);

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        if ($this->entityLangs->count() <= 0) {
            return '';
        }

        $flagLang = $this->entityLangs->first();

        return $flagLang->getTitle();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        if ($this->entityLangs->count() <= 0) {
            return '';
        }

        $flagLang = $this->entityLangs->first();

        return $flagLang->getDescription();
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        if ($this->entityLangs->count() <= 0) {
            return '';
        }

        $flagLang = $this->entityLangs->first();

        return $flagLang->getLink();
    }

    /**
     * Get the config as an associative array.
     *
     * @return array|null
     */
    public function getConfig(): ?array
    {
        return $this->config !== null ? json_decode($this->config, true) : null;
    }

    /**
     * Set the config from an associative array.
     *
     * @param array|null $config
     */
    public function setConfig(?array $config): void
    {
        $this->config = $config !== null ? json_encode($config) : null;
    }

    /**
     * @return bool
     */
    public function getEnable(): bool
    {
        return $this->enable;
    }

    /**
     * @param bool $enable
     */
    public function setEnable(bool $enable): void
    {
        $this->enable = $enable;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getHook(): int
    {
        return $this->hook;
    }

    /**
     * @param int $hook
     */
    public function setHook(int $hook): void
    {
        $this->hook = $hook;
    }

    /**
     * @param Shop $shop
     */
    public function addShop(Shop $shop): void
    {
        $this->shops[] = $shop;
    }

    /**
     * @param Shop $shop
     */
    public function removeShop(Shop $shop): void
    {
        $this->shops->removeElement($shop);
    }

    /**
     * @return Collection
     */
    public function getShops(): Collection
    {
        return $this->shops;
    }

    /**
     * @return void
     */
    public function clearShops(): void
    {
        $this->shops->clear();
    }

    /**
     * Get the value of fromDate.
     *
     * @return \DateTime|null
     */
    public function getFromDate(): ?\DateTime
    {
        return $this->fromDate;
    }

    /**
     * Set the value of fromDate.
     *
     * @param \DateTime|null $fromDate
     *
     * @return self
     */
    public function setFromDate(?\DateTime $fromDate): self
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    /**
     * Get the value of toDate.
     *
     * @return \DateTime|null
     */
    public function getToDate(): ?\DateTime
    {
        return $this->toDate;
    }

    /**
     * Set the value of toDate.
     *
     * @param \DateTime|null $toDate
     *
     * @return self
     */
    public function setToDate(?\DateTime $toDate): self
    {
        $this->toDate = $toDate;

        return $this;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function getCategoryIds(): array
    {
        return $this->categories->map(function ($category) {
            return $category->getCategoryId();
        })->toArray();
    }

    public function addCategory(int $categoryId): self
    {
        // Sprawdzenie, czy kategoria już istnieje
        foreach ($this->categories as $category) {
            if ($category->getCategoryId() === $categoryId) {
                return $this;
            }
        }

        // Jeśli nie istnieje, dodaj nową
        $category = new IqitProductFlagCategory();
        $category->setFlag($this);
        $category->setCategoryId($categoryId);
        $this->categories->add($category);

        return $this;
    }

    public function removeCategory(int $categoryId): self
    {
        foreach ($this->categories as $category) {
            if ($category->getCategoryId() === $categoryId) {
                $this->categories->removeElement($category);
            }
        }

        return $this;
    }

    /**
     * Clears all categories associated with this flag.
     */
    public function clearCategories(): void
    {
        foreach ($this->categories as $category) {
            $this->categories->removeElement($category);
        }
    }
}
