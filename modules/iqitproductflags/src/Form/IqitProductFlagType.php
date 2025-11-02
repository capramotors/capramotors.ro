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

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShopBundle\Form\Admin\Type\CategoryChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;

use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;


class IqitProductFlagType extends AbstractType
{
    use TranslatorAwareTrait;
    
    public function __construct(
        private readonly ShopContext $shopContext
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'enable',
                SwitchType::class,
                [
                    'label' => 'Enable',
                ]
            )
            ->add('title', TranslatableType::class, [
                'label' => 'Title',
                'translation_domain' => 'Modules.Iqitproductflags.Config',
                'constraints' => [
                    new DefaultLanguage([
                        'message' => $this->trans(
                            'The field %field_name% is required at least in your default language.', [],
                            'Admin.Notifications.Error',
                            [
                                '%field_name%' => sprintf(
                                    '"%s"',
                                    $this->trans('Content', [], 'Modules.Iqitproductflags.Config')
                                ),
                            ]
                        ),
                    ]),
                ],
            ])
            ->add('description', TranslatableType::class, [
                'type' => TextareaType::class,
                'label' => 'Description',
                'translation_domain' => 'Modules.Iqitproductflags.Config',
                'help' => 'Visible only when placed in highlighted position',
                'required' => false,
            ])
            ->add('link', TranslatableType::class, [
                'label' => 'Link',
                'translation_domain' => 'Modules.Iqitproductflags.Config',
                'required' => false,
            ])
            ->add('from_date', DatePickerType::class, [
                'label' => 'From',
                'translation_domain' => 'Modules.Iqitproductflags.Config',
                'required' => false,
            ])
            ->add('to_date', DatePickerType::class, [
                'label' => 'To',
                'translation_domain' => 'Modules.Iqitproductflags.Config',
                'required' => false,
            ])
            ->add('hook', ChoiceType::class, [
                'label' => 'Placement',
                'required' => true,
                'translation_domain' => 'Modules.Iqitproductflags.Config',
                'choices' => [
                    'Miniature, product(small)' => 0,
                    'Miniature, product(highlight)' => 1,
                    'Product(small)' => 2,
                    'Product(highlight)' => 3,
                    'Miniature' => 4,
                ],
            ])
            ->add('config_bg_color', ColorType::class, [
                'label' => 'Background',
                'attr' => ['class' => 'col-md-4 col-lg-2 p-1 h-25 w-50'],
                'translation_domain' => 'Modules.Iqitproductflags.Config',
                'required' => false,
            ])
            ->add('config_txt_color', ColorType::class, [
                'label' => 'Text color',
                'attr' => ['class' => 'col-md-4 col-lg-2 p-1 h-25 w-50'],
                'translation_domain' => 'Modules.Iqitproductflags.Config',
                'required' => false,
            ])
            ->add('categories', CategoryChoiceTreeType::class, [
                'label' => 'Categories',
                'help' => 'When there is no catgory selected, flag is global',
                'translation_domain' => 'Modules.Iqitproductflags.Config',
                'required' => false,
                'multiple' => true,
            ]);
            if ($this->shopContext->isMultiShopUsed()) {
            $builder->add(
                'shop_association',
                ShopChoiceTreeType::class,
                [
                    'label' => 'Shop associations',
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->trans(
                                'You have to select at least one shop to associate this item with', [], 
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ]
            );
        }
    }
}
