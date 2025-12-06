<?php

declare(strict_types=1);

namespace PrestaShop\Module\TbiroPayment\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TbiroPaymentConfigurationController extends FrameworkBundleAdminController
{
    public function index(Request $request): Response
    {
        $textFormDataHandler = $this->get('prestashop.module.tbiropayment.tbiropayment_configuration_form_handler');

        $textForm = $textFormDataHandler->getForm();
        $textForm->handleRequest($request);

        if ($textForm->isSubmitted() && $textForm->isValid()) {
            $errors = $textFormDataHandler->save($textForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('tbiro_payment_configuration_form');
            }

            $this->flashErrors($errors);
        }

        return $this->render('@Modules/tbiropayment/views/templates/admin/form.html.twig', [
            'tbiroPaymentConfigurationForm' => $textForm->createView()
        ]);
    }
}
