<?php

declare(strict_types=1);

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Khatovar\Bundle\UserBundle\Form\Factory;

use Khatovar\Component\User\Application\Query\GetUserRoles;
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
    private $formFactory;

    /** @var GetUserRoles */
    private $getUserRoles;

    /** @var RouterInterface */
    private $router;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface      $router
     * @param TranslatorInterface  $translator
     * @param GetUserRoles         $getUserRoles
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        TranslatorInterface $translator,
        GetUserRoles $getUserRoles
    ) {
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->translator = $translator;
        $this->getUserRoles = $getUserRoles;
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
                    'choices' => $this->getUserRoles->available(),
                    'label' => $this->translator->trans('khatovar_user.form.role.label'),
                    'data' => $currentRole,
                ]
            )
            ->add('submit', SubmitType::class, ['label' => $this->translator->trans('khatovar_user.button.change')])
            ->getForm();

        return $form;
    }
}
