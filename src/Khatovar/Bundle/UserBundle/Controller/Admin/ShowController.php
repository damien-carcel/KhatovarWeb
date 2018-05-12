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
use Khatovar\Bundle\UserBundle\Form\Factory\UserFormFactory;
use Khatovar\Bundle\UserBundle\Query\GetUser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

/**
 * Shows a user profile.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class ShowController
{
    /** @var GetUser */
    private $getUser;

    /** @var UserFormFactory */
    private $userFormFactory;

    /** @var Environment */
    private $twig;

    /**
     * @param GetUser         $getUser
     * @param UserFormFactory $userFormFactory
     * @param Environment     $twig
     */
    public function __construct(GetUser $getUser, UserFormFactory $userFormFactory, Environment $twig)
    {
        $this->getUser = $getUser;
        $this->userFormFactory = $userFormFactory;
        $this->twig = $twig;
    }

    /**
     * @param string $username
     *
     * @throws NotFoundHttpException When there is no user names "$username"
     * @throws \Twig_Error_Loader    When the template cannot be found
     * @throws \Twig_Error_Syntax    When an error occurred during compilation
     * @throws \Twig_Error_Runtime   When an error occurred during rendering
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

        $content = $this->twig->render(
            'KhatovarUserBundle:Admin:show.html.twig',
            ['user' => $user]
        );

        return new Response($content);
    }
}
