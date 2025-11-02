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

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class IqitProductFlagCategory
{
    /**
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="Iqit\IqitProductFlags\Entity\IqitProductFlag", inversedBy="categories")
     *
     * @ORM\JoinColumn(name="id_iqit_product_flag", referencedColumnName="id_iqit_product_flag", onDelete="CASCADE")
     */
    private IqitProductFlag $flag;

    /**
     * @ORM\Id
     *
     * @ORM\Column(type="integer", name="id_category")
     */
    private int $categoryId;

    public function getFlag(): IqitProductFlag
    {
        return $this->flag;
    }

    public function setFlag(IqitProductFlag $flag): self
    {
        $this->flag = $flag;

        return $this;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function setCategoryId(int $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }
}
