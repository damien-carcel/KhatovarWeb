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

use Khatovar\Bundle\UserBundle\Event\UserEvents;
use Khatovar\Bundle\UserBundle\Form\Factory\UserFormFactory;
use Khatovar\Bundle\UserBundle\Query\Exception\UserDoesNotExist;
use Khatovar\Bundle\UserBundle\Query\GetUserFromDatabase;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class RemoveController
{
    /** @var GetUserFromDatabase */
    private $getUser;

    /** @var UserFormFactory */
    private $userFormFactory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var RegistryInterface */
    private $doctrine;

    /** @var Session */
    private $session;

    /** @var TranslatorInterface */
    private $translator;

    /** @var RouterInterface */
    private $router;

    /**
     * @param GetUserFromDatabase      $getUser
     * @param UserFormFactory          $userFormFactory
     * @param EventDispatcherInterface $eventDispatcher
     * @param RegistryInterface        $doctrine
     * @param Session                  $session
     * @param TranslatorInterface      $translator
     * @param RouterInterface          $router
     */
    public function __construct(
        GetUserFromDatabase $getUser,
        UserFormFactory $userFormFactory,
        EventDispatcherInterface $eventDispatcher,
        RegistryInterface $doctrine,
        Session $session,
        TranslatorInterface $translator,
        RouterInterface $router
    ) {
        $this->getUser = $getUser;
        $this->userFormFactory = $userFormFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->doctrine = $doctrine;
        $this->session = $session;
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @param string  $username
     *
     * @throws NotFoundHttpException When there is no user names "$username"
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

        $form = $this->userFormFactory->createDeleteForm($username, 'khatovar_user_admin_remove');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->eventDispatcher->dispatch(UserEvents::PRE_REMOVE, new GenericEvent($user));

            $this->doctrine->getManager()->remove($user);
            $this->doctrine->getManager()->flush();

            $this->eventDispatcher->dispatch(UserEvents::POST_REMOVE, new GenericEvent($user));

            $this->session->getFlashBag()->add(
                'notice',
                $this->translator->trans('khatovar_user.notice.delete.label')
            );
        }

        $redirectRoute = $this->router->generate(
            'khatovar_user_admin_index',
            [],
            UrlGeneratorInterface::ABSOLUTE_PATH
        );

        return new RedirectResponse($redirectRoute, 302);
    }
}
