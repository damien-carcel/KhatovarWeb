<?php

declare(strict_types=1);

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Khatovar\Bundle\UserBundle\Form\Factory;

use Khatovar\Bundle\UserBundle\Manager\RolesManager;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Creates forms for User entity.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserFormFactory
{
    /** @var FormFactoryInterface */
    protected $formFactory;

    /** @var RolesManager */
    protected $rolesManager;

    /** @var RouterInterface */
    protected $router;

    /** @var TranslatorInterface */
    protected $translator;

    /**
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface      $router
     * @param TranslatorInterface  $translator
     * @param RolesManager         $rolesManager
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        TranslatorInterface $translator,
        RolesManager $rolesManager
    ) {
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->translator = $translator;
        $this->rolesManager = $rolesManager;
    }

    /**
     * Creates a form to create a new entity.
     *
     * @param UserInterface $item the entity to create
     * @param string        $type The form type to use with the entity
     * @param string        $url  The route used to create the entity
     *
     * @return FormInterface
     */
    public function createCreateForm(UserInterface $item, string $type, string $url): FormInterface
    {
        $form = $this->formFactory->create(
            $type,
            $item,
            [
                'action' => $this->router->generate($url),
                'method' => 'POST',
            ]
        );

        return $form;
    }

    /**
     * Creates a form to edit an entity.
     *
     * @param UserInterface $item the entity to edit
     * @param string        $type The form type to use with the entity
     * @param string        $url  The route used to edit the entity
     *
     * @return FormInterface
     */
    public function createEditForm(UserInterface $item, string $type, string $url): FormInterface
    {
        $form = $this->formFactory->create(
            $type,
            $item,
            [
                'action' => $this->router->generate($url, ['username' => $item->getUsername()]),
                'method' => 'PUT',
            ]
        );

        return $form;
    }

    /**
     * Creates a form to delete an entity.
     *
     * @param string $username The ID of the entity to delete
     * @param string $url      The route used to delete the entity
     *
     * @return FormInterface
     */
    public function createDeleteForm(string $username, string $url): FormInterface
    {
        $formBuilder = $this->formFactory->createBuilder();

        $formBuilder->setAction($this->router->generate($url, ['username' => $username]));
        $formBuilder->setMethod('DELETE');
        $formBuilder->add(
            'submit',
            SubmitType::class,
            [
                'label' => $this->translator->trans('khatovar_user.button.delete'),
                'attr' => [
                    'class' => 'btn btn-sm btn-default',
                    'onclick' => sprintf(
                        'return confirm("%s")',
                        $this->translator->trans('khatovar_user.notice.delete.confirmation')
                    ),
                ],
            ]
        );

        return $formBuilder->getForm();
    }

    /**
     * Return a list of delete forms for a set entities.
     *
     * @param UserInterface[] $items The list of entities to delete
     * @param string          $url   The route used to delete the entities
     *
     * @return FormInterface[]
     */
    public function createDeleteFormViews(array $items, string $url): array
    {
        $deleteForms = [];

        foreach ($items as $item) {
            $deleteForms[$item->getUsername()] = $this->createDeleteForm($item->getUsername(), $url)->createView();
        }

        return $deleteForms;
    }

    /**
     * Creates a form to set user's roles.
     *
     * @param string $currentRole
     *
     * @return FormInterface
     */
    public function createSetRoleForm(string $currentRole): FormInterface
    {
        $form = $this->formFactory
            ->createBuilder()
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' => $this->rolesManager->getChoices(),
                    'label' => $this->translator->trans('khatovar_user.form.role.label'),
                    'data' => $currentRole,
                ]
            )
            ->add('submit', SubmitType::class, ['label' => $this->translator->trans('khatovar_user.button.change')])
            ->getForm();

        return $form;
    }
}
