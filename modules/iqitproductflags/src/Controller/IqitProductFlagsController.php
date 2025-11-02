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

namespace Iqit\IqitProductFlags\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Iqit\IqitProductFlags\Entity\IqitProductFlag;
use Iqit\IqitProductFlags\Repository\FlagRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandler;
use PrestaShop\PrestaShop\Core\Grid\GridFactory;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Grid\Position\GridPositionUpdater;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdate;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdateFactory;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteria;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Entity\Repository\ShopRepository;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IqitProductFlagsController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function index(#[Autowire(service: 'iqit.iqitproductflags.grid.iqit_product_flag_grid_factory')]
        GridFactory $flagGridFactory, Request $request): Response
    {
        $flagGrid = $flagGridFactory->getGrid(new SearchCriteria([], 'position', 'asc'));

        return $this->render('@Modules/iqitproductflags/views/templates/admin/index.html.twig', [
            'title' => 'Content block list',
            'contentBlockGrid' => $this->presentGrid($flagGrid),
            'help_link' => false,
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))")]
    public function create(
        #[Autowire(service: 'iqit.iqitproductflags.form.identifiable_object.builder.iqit_product_flag_form_builder')]
        FormBuilder $formBuilder,
        #[Autowire(service: 'iqit.iqitproductflags.form.identifiable_object.handler.iqit_product_flag_form_handler')]
        FormHandler $formHandler,
        Request $request,
    ): Response {
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        $result = $formHandler->handle($form);

        if (null !== $result->getIdentifiableObjectId()) {
            $this->addFlash(
                'success',
                $this->trans('Successful creation.', [], 'Admin.Notifications.Success')
            );
            $this->clearModuleCache();

            return $this->redirectToRoute('iqitproductflags');
        }

        return $this->render('@Modules/iqitproductflags/views/templates/admin/form.html.twig', [
            'entityForm' => $form->createView(),
            'title' => 'Content block creation',
            'help_link' => false,
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))")]
    public function edit(
        #[Autowire(service: 'iqit.iqitproductflags.form.identifiable_object.builder.iqit_product_flag_form_builder')]
        FormBuilder $formBuilder,
        #[Autowire(service: 'iqit.iqitproductflags.form.identifiable_object.handler.iqit_product_flag_form_handler')]
        FormHandler $formHandler,
        Request $request,
        int $flagId,
    ): Response {
        $form = $formBuilder->getFormFor((int) $flagId);
        $form->handleRequest($request);

        $result = $formHandler->handleFor($flagId, $form);

        if (null !== $result->getIdentifiableObjectId()) {
            $this->addFlash(
                'success',
                $this->trans('Successful edition.', [], 'Admin.Notifications.Success')
            );
            $this->clearModuleCache();

            return $this->redirectToRoute('iqitproductflags');
        }

        return $this->render('@Modules/iqitproductflags/views/templates/admin/form.html.twig', [
            'entityForm' => $form->createView(),
            'title' => 'Content block edition',
            'help_link' => false,
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))")]
    public function delete(
        #[Autowire(service: 'iqit.iqitproductflags.repository.flags_repository')]
        FlagRepository $flagsRepository,
        EntityManagerInterface $entityManager,
        #[Autowire(service: 'prestashop.adapter.shop.context')]
        Context $multistoreContext,
        #[Autowire(service: 'prestashop.core.admin.shop.repository')]
        ShopRepository $shopRepository,
        Request $request,
        int $flagId,
    ): Response {
        try {
            // @phpstan-ignore-next-line
            $flag = $flagsRepository->findOneById($flagId);
        } catch (EntityNotFoundException $e) {
            $flag = null;
        }

        if (null !== $flag) {
            if ($multistoreContext->isAllShopContext()) {
                $flag->clearShops();
                $entityManager->remove($flag);
            } else {
                $shopList = $shopRepository->findBy(['id' => $multistoreContext->getContextListShopID()]);
                foreach ($shopList as $shop) {
                    $flag->removeShop($shop);
                    $entityManager->flush();
                }
                if (count($flag->getShops()) === 0) {
                    $entityManager->remove($flag);
                }
            }
            $entityManager->flush();
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', [], 'Admin.Notifications.Success')
            );
            $this->clearModuleCache();

            return $this->redirectToRoute('iqitproductflags');
        }

        $this->addFlash(
            'error',
            sprintf(
                'Cannot find content block %d',
                $flagId
            )
        );

        return $this->redirectToRoute('iqitproductflags');
    }

    /**
     * @param Request $request
     * @param int $flagId
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))")]
    public function toggleStatus(#[Autowire(service: 'doctrine.orm.entity_manager')]
        EntityManager $entityManager, Request $request, int $flagId): Response
    {
        $flag = $entityManager
            ->getRepository(IqitProductFlag::class)
            ->findOneBy(['id' => $flagId]);

        if (empty($flag)) {
            return $this->json([
                'status' => false,
                'message' => sprintf('Content block %d doesn\'t exist', $flagId),
            ]);
        }

        try {
            $flag->setEnable(!$flag->getEnable());
            $entityManager->flush();
            $response = [
                'status' => true,
                'message' => $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success'),
            ];
            $this->clearModuleCache();
        } catch (\Exception $e) {
            $response = [
                'status' => false,
                'message' => sprintf(
                    'There was an error while updating the status of content block %d: %s',
                    $flagId,
                    $e->getMessage()
                ),
            ];
        }

        return $this->json($response);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))")]
    public function updatePositions(
        #[Autowire(service: 'PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdateFactory')]
        PositionUpdateFactory $positionUpdateFactory,
        #[Autowire(service: 'PrestaShop\PrestaShop\Core\Grid\Position\GridPositionUpdater')]
        GridPositionUpdater $updater,
        Request $request,
    ): Response {
        $positionsData = [
            'positions' => $request->request->all()['positions'],
        ];

        $positionDefinition = new PositionDefinition(
            'iqit_product_flag',
            'id_iqit_product_flag',
            'position'
        );

        try {
            /** @var PositionUpdate $positionUpdate */
            $positionUpdate = $positionUpdateFactory->buildPositionUpdate($positionsData, $positionDefinition);
        } catch (PositionDataException $e) {
            $errors = [$e->toArray()];
            $this->addFlashErrors($errors);

            return $this->redirectToRoute('iqitproductflags');
        }

        try {
            $updater->update($positionUpdate);
            $this->clearModuleCache();
            $this->addFlash('success', $this->trans('Successful update.', [], 'Admin.Notifications.Success'));
        } catch (PositionUpdateException $e) {
            $errors = [$e->toArray()];
            $this->addFlashErrors($errors);
        }

        return $this->redirectToRoute('iqitproductflags');
    }

    /**
     * clearModuleCache
     *
     * @return void
     */
    public function clearModuleCache()
    {
    }
}
