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

namespace Iqit\IqitProductVariants\Form;

interface FormDataHandlerInterface
{
    /*
     * @param array $data form data
     *
     * @return bool
     *
     * @throws ModuleErrorException
     */
    public function save(array $data): bool;

    /*
    * @param array $params
    *
    * @return array
    */
    public function getData(array $params): array;
}
