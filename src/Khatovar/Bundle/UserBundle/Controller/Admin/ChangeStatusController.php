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
use Khatovar\Component\User\Application\Command\UserStatus;
use Khatovar\Component\User\Application\Command\UserStatusHandler;
use Khatovar\Component\User\Application\Query\CurrentTokenUser;
use Khatovar\Component\User\Application\Query\GetUser;
use Khatovar\Component\User\Domain\Event\UserEvents;
use Khatovar\Component\User\Domain\Exception\UserDoesNotExist;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Activates or deactivates a user.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class ChangeStatusController
{
    /** @var GetUser */
    private $getUser;

    /** @var UserFormFactory */
    private $userFormFactory;

    /** @var UserStatusHandler */
    private $userStatusHandler;

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

    /**
     * @param GetUser                  $getUser
     * @param UserFormFactory          $userFormFactory
     * @param UserStatusHandler        $userStatusHandler
     * @param CurrentTokenUser         $currentTokenUser
     * @param EventDispatcherInterface $eventDispatcher
     * @param Session                  $session
     * @param TranslatorInterface      $translator
     * @param RouterInterface          $router
     */
    public function __construct(
        GetUser $getUser,
        UserFormFactory $userFormFactory,
        UserStatusHandler $userStatusHandler,
        CurrentTokenUser $currentTokenUser,
        EventDispatcherInterface $eventDispatcher,
        Session $session,
        TranslatorInterface $translator,
        RouterInterface $router
    ) {
        $this->getUser = $getUser;
        $this->userFormFactory = $userFormFactory;
        $this->userStatusHandler = $userStatusHandler;
        $this->currentTokenUser = $currentTokenUser;
        $this->eventDispatcher = $eventDispatcher;
        $this->session = $session;
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * @param string $username
     *
     * @throws NotFoundHttpException When there is no user names "$username"
     *
     * @return Response
     */
    public function __invoke(string $username): Response
    {
        try {
            $user = $this->getUser->byUsername($username);
        } catch (UserDoesNotExist $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }

        if (!$this->currentTokenUser->isSuperAdmin() && $user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedException(
                'You do not have the permission to activate or deactivate an administrator.'
            );
        }

        if ($user->isEnabled()) {
            $this->eventDispatcher->dispatch(UserEvents::PRE_DEACTIVATE, new GenericEvent($user));

            $this->userStatusHandler->handle(new UserStatus($user, false));
            $notice = 'khatovar_user.notice.deactivated';

            $this->eventDispatcher->dispatch(UserEvents::POST_DEACTIVATE, new GenericEvent($user));
        } else {
            $this->eventDispatcher->dispatch(UserEvents::PRE_ACTIVATE, new GenericEvent($user));

            $this->userStatusHandler->handle(new UserStatus($user, true));
            $notice = 'khatovar_user.notice.activated';

            $this->eventDispatcher->dispatch(UserEvents::POST_ACTIVATE, new GenericEvent($user));
        }

        $this->session->getFlashBag()->add('notice', $this->translator->trans($notice));

        $redirectRoute = $this->router->generate(
            'khatovar_user_admin_index',
            [],
            UrlGeneratorInterface::ABSOLUTE_PATH
        );

        return new RedirectResponse($redirectRoute, 302);
    }
}
