<?php

declare(strict_types=1);

namespace PrestaShop\Module\TbiroPayment\Form;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class TbiroPaymentConfigurationType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'credittbiro_status',
                SwitchType::class,
                [
                    'label' => $this->trans('Enable/Disable the tbi bank RO module', 'Modules.Tbiropayment.Admin'),
                    'help' => $this->trans('Enable/Disable the tbi bank RO module', 'Modules.Tbiropayment.Admin'),
                ]
            )
            ->add(
                'credittbiro_show_status',
                SwitchType::class,
                [
                    'label' => $this->trans('Show button', 'Modules.Tbiropayment.Admin'),
                    'help' => $this->trans('Show module button on product page', 'Modules.Tbiropayment.Admin'),
                ]
            )
            ->add('credittbiro_unicid', TextType::class, [
                'label' => $this->trans('Unique shop identifier', 'Modules.Tbiropayment.Admin'),
                'help' => $this->trans('Unique shop identifier in the tbi bank system', 'Modules.Tbiropayment.Admin'),
                'required' => true,
            ])
            ->add('credittbiro_store_id', TextType::class, [
                'label' => $this->trans('Store ID for eCommerce tbi bank system', 'Modules.Tbiropayment.Admin'),
                'help' => $this->trans('Store ID for eCommerce tbi bank system. Required for system authentication', 'Modules.Tbiropayment.Admin'),
                'required' => true,
            ])
            ->add('credittbiro_username', TextType::class, [
                'label' => $this->trans('Username for eCommerce tbi bank system', 'Modules.Tbiropayment.Admin'),
                'help' => $this->trans('Username for eCommerce tbi bank system. Required for system authentication', 'Modules.Tbiropayment.Admin'),
                'required' => true,
            ])
            ->add('credittbiro_password', TextType::class, [
                'label' => $this->trans('Password for eCommerce tbi bank system', 'Modules.Tbiropayment.Admin'),
                'help' => $this->trans('Password for eCommerce tbi bank system. Required for system authentication', 'Modules.Tbiropayment.Admin'),
                'required' => true,
            ])
            ->add('credittbiro_firstlabel', TextType::class, [
                'label' => $this->trans('First label next to payment button', 'Modules.Tbiropayment.Admin'),
                'help' => $this->trans('The first text that appears to the right of the tbi bank payment button', 'Modules.Tbiropayment.Admin'),
                'data' => $this->trans('tbi bank: Cumpara acum, plateste mai tarziu', 'Modules.Tbiropayment.Admin'),
                'required' => true,
            ])
            ->add('credittbiro_secondlabel', TextType::class, [
                'label' => $this->trans('Second label next to payment button', 'Modules.Tbiropayment.Admin'),
                'help' => $this->trans('The second text that appears to the right of the tbi bank payment button', 'Modules.Tbiropayment.Admin'),
                'data' => $this->trans('100% online, fara hartii sau drumuri la banca', 'Modules.Tbiropayment.Admin'),
                'required' => true,
            ])
            ->add('creditirisro_firstlabel', TextType::class, [
                'label' => $this->trans('First label next to IRIS payment button', 'Modules.Tbiropayment.Admin'),
                'help' => $this->trans('The first text that appears to the right of the IRIS Pay RO payment button', 'Modules.Tbiropayment.Admin'),
                'required' => false,
            ])
            ->add('creditirisro_secondlabel', TextType::class, [
                'label' => $this->trans('Second label next to IRIS payment button', 'Modules.Tbiropayment.Admin'),
                'help' => $this->trans('The second text that appears to the right of the IRIS Pay RO payment button', 'Modules.Tbiropayment.Admin'),
                'required' => false,
            ])
            ->add('credittbiro_iris_iban', TextType::class, [
                'label' => $this->trans('IRIS IBAN', 'Modules.Tbiropayment.Admin'),
                'help' => $this->trans('IRIS IBAN', 'Modules.Tbiropayment.Admin'),
                'required' => false,
            ])
            ->add('credittbiro_iris_key', TextType::class, [
                'label' => $this->trans('IRIS Key', 'Modules.Tbiropayment.Admin'),
                'help' => $this->trans('IRIS Key', 'Modules.Tbiropayment.Admin'),
                'required' => false,
            ]);
    }
}
