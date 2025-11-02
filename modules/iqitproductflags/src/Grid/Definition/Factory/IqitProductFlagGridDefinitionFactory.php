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

namespace Iqit\IqitProductFlags\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\PositionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;

class IqitProductFlagGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'iqit_grid';

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Flags', [], 'Modules.Iqitproductflags.Config');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $positionColumn = new PositionColumn('position');
        $positionColumn->setName($this->trans('Position', [], 'Admin.Global'));
        $positionColumn->setOptions([
            'id_field' => 'id_iqit_product_flag',
            'position_field' => 'position',
            'update_route' => 'iqitproductflags_update_positions',
            'update_method' => 'POST',
        ]);

        return (new ColumnCollection())
            ->add(
                (new DataColumn('id_iqit_product_flag'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_iqit_product_flag',
                    ])
            )
            ->add(
                (new DataColumn('title'))
                    ->setName($this->trans('Title', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'title',
                    ])
            )
            ->add(
                (new DataColumn('link'))
                    ->setName($this->trans('Link', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'link',
                    ])
            )
            ->add(
                (new DataColumn('from_date'))
                    ->setName($this->trans('From date', [], 'Modules.Iqitproductflags.Config'))
                    ->setOptions([
                        'field' => 'from_date',
                    ])
            )
            ->add(
                (new DataColumn('to_date'))
                    ->setName($this->trans('To date', [], 'Modules.Iqitproductflags.Config'))
                    ->setOptions([
                        'field' => 'to_date',
                    ])
            )
            ->add(
                (new ToggleColumn('enable'))
                    ->setName($this->trans('Displayed', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'enable',
                        'primary_field' => 'id_iqit_product_flag',
                        'route' => 'iqitproductflags_toggle_status',
                        'route_param_name' => 'flagId',
                    ])
            )
            ->add($positionColumn)
            ->add(
                (new ActionColumn('actions'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                (new LinkRowAction('edit'))
                                    ->setIcon('edit')
                                    ->setOptions([
                                        'route' => 'iqitproductflags_edit',
                                        'route_param_name' => 'flagId',
                                        'route_param_field' => 'id_iqit_product_flag',
                                    ])
                            )
                            ->add(
                                (new LinkRowAction('delete'))
                                    ->setName($this->trans('Delete', [], 'Admin.Actions'))
                                    ->setIcon('delete')
                                    ->setOptions([
                                        'route' => 'iqitproductflags_delete',
                                        'route_param_name' => 'flagId',
                                        'route_param_field' => 'id_iqit_product_flag',
                                        'confirm_message' => $this->trans(
                                            'Delete selected item?',
                                            [],
                                            'Admin.Notifications.Warning'
                                        ),
                                    ])
                            ),
                    ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return new FilterCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return new GridActionCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return new BulkActionCollection();
    }
}
