<?php

declare(strict_types=1);

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2018 Damien Carcel <damien.carcel@gmail.com>
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

namespace Khatovar\Bundle\UserBundle\Controller\Admin;

use Khatovar\Bundle\UserBundle\Form\Factory\UserFormFactory;
use Khatovar\Component\User\Application\Command\SetRole;
use Khatovar\Component\User\Application\Command\SetRoleHandler;
use Khatovar\Component\User\Application\Query\CurrentTokenUser;
use Khatovar\Component\User\Application\Query\GetUser;
use Khatovar\Component\User\Application\Query\GetUserRoles;
use Khatovar\Component\User\Domain\Event\UserEvents;
use Khatovar\Component\User\Domain\Exception\UserDoesNotExist;
use Khatovar\Component\User\Domain\Exception\UserRoleDoesNotExist;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Sets/change the user's role(s).
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class SetRoleController
{
    /** @var GetUser */
    private $getUser;

    /** @var UserFormFactory */
    private $userFormFactory;

    /** @var SetRoleHandler */
    private $setRoleHandler;

    /** @var GetUserRoles */
    private $userRole;

    /** @var CurrentTokenUser */
    private $currentTokenUser;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var Session */
    private $session;

    /** @var TranslatorInterface */
    private $translator;

    /** @var RouterInterface */
    private $router;

    /** @var Environment */
    private $twig;

    public function __construct(
        GetUser $getUser,
        UserFormFactory $userFormFactory,
        SetRoleHandler $setRoleHandler,
        GetUserRoles $userRole,
        CurrentTokenUser $currentTokenUser,
        EventDispatcherInterface $eventDispatcher,
        Session $session,
        TranslatorInterface $translator,
        RouterInterface $router,
        Environment $twig
    ) {
        $this->getUser = $getUser;
        $this->userFormFactory = $userFormFactory;
        $this->setRoleHandler = $setRoleHandler;
        $this->userRole = $userRole;
        $this->currentTokenUser = $currentTokenUser;
        $this->eventDispatcher = $eventDispatcher;
        $this->session = $session;
        $this->translator = $translator;
        $this->router = $router;
        $this->twig = $twig;
    }

    /**
     * @throws NotFoundHttpException When there is no user names "$username"
     * @throws \Twig_Error_Loader    When the template cannot be found
     * @throws \Twig_Error_Syntax    When an error occurred during compilation
     * @throws \Twig_Error_Runtime   When an error occurred during rendering
     */
    public function __invoke(Request $request, string $username): Response
    {
        try {
            $user = $this->getUser->byUsername($username);
        } catch (UserDoesNotExist $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }

        if (!$this->currentTokenUser->isSuperAdmin() && $user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have the permission to change the role of an administrator.');
        }

        $role = $this->userRole->forUser($user);

        $form = $this->userFormFactory->createSetRoleForm($role);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $selectedRole = $form->getData();

            $this->eventDispatcher->dispatch(UserEvents::PRE_SET_ROLE, new GenericEvent($user));

            try {
                $this->setRoleHandler->handle(new SetRole($user, $selectedRole['roles']));
            } catch (UserRoleDoesNotExist $e) {
                throw new InvalidArgumentException($e->getMessage(), 0, $e);
            }

            $this->eventDispatcher->dispatch(UserEvents::POST_SET_ROLE, new GenericEvent($user));

            $this->session->getFlashBag()->add('notice', $this->translator->trans('khatovar_user.notice.set_role'));

            $redirectRoute = $this->router->generate(
                'khatovar_user_admin_index',
                [],
                UrlGeneratorInterface::ABSOLUTE_PATH
            );

            return new RedirectResponse($redirectRoute, 302);
        }

        $content = $this->twig->render(
            'KhatovarUserBundle:Admin:set_role.html.twig',
            [
                'form' => $form->createView(),
                'username' => $user->getUsername(),
            ]
        );

        return new Response($content);
    }
}
