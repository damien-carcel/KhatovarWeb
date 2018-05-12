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

use Khatovar\Bundle\UserBundle\Entity\Exception\UserDoesNotExist;
use Khatovar\Bundle\UserBundle\Entity\UserInterface;
use Khatovar\Bundle\UserBundle\Event\UserEvents;
use Khatovar\Bundle\UserBundle\Form\Factory\UserFormFactory;
use Khatovar\Bundle\UserBundle\Manager\RolesManager;
use Khatovar\Bundle\UserBundle\Manager\UserManager;
use Khatovar\Bundle\UserBundle\Query\GetUser;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
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

    /** @var UserManager */
    private $userManager;

    /** @var RolesManager */
    private $rolesManager;

    /** @var TokenStorageInterface */
    private $tokenStorage;

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

    /**
     * @param GetUser                  $getUser
     * @param UserFormFactory          $userFormFactory
     * @param UserManager              $userManager
     * @param RolesManager             $rolesManager
     * @param TokenStorageInterface    $tokenStorage
     * @param EventDispatcherInterface $eventDispatcher
     * @param Session                  $session
     * @param TranslatorInterface      $translator
     * @param RouterInterface          $router
     * @param Environment              $twig
     */
    public function __construct(
        GetUser $getUser,
        UserFormFactory $userFormFactory,
        UserManager $userManager,
        RolesManager $rolesManager,
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $eventDispatcher,
        Session $session,
        TranslatorInterface $translator,
        RouterInterface $router,
        Environment $twig
    ) {
        $this->getUser = $getUser;
        $this->userFormFactory = $userFormFactory;
        $this->userManager = $userManager;
        $this->rolesManager = $rolesManager;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
        $this->session = $session;
        $this->translator = $translator;
        $this->router = $router;
        $this->twig = $twig;
    }

    /**
     * @param Request $request
     * @param string  $username
     *
     * @throws NotFoundHttpException When there is no user names "$username"
     * @throws \Twig_Error_Loader    When the template cannot be found
     * @throws \Twig_Error_Syntax    When an error occurred during compilation
     * @throws \Twig_Error_Runtime   When an error occurred during rendering
     *
     * @return Response
     */
    public function __invoke(Request $request, string $username): Response
    {
        try {
            $user = $this->getUser->byUsername($username);
        } catch (UserDoesNotExist $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }

        if (!$this->getFromTokenStorage()->isSuperAdmin() && $user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have the permission to change the role of an administrator.');
        }

        $userRole = $this->rolesManager->getUserRole($user);

        $form = $this->userFormFactory->createSetRoleForm($userRole);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $selectedRole = $form->getData();

            $this->eventDispatcher->dispatch(UserEvents::PRE_SET_ROLE, new GenericEvent($user));

            $this->userManager->setRole($user, $selectedRole);

            $this->eventDispatcher->dispatch(UserEvents::POST_SET_ROLE, new GenericEvent($user));

            $this->session->getFlashBag()->add(
                'notice',
                $this->translator->trans('khatovar_user.notice.set_role')
            );

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

    /**
     * Gets a user from the Security Token Storage.
     *
     * @return UserInterface|null
     */
    private function getFromTokenStorage(): ?UserInterface
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }
}
