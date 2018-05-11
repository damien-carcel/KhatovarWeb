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
use Khatovar\Bundle\UserBundle\Manager\UserManager;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * Renders the list of administrated users.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class IndexController
{
    /** @var UserManager */
    private $userManager;

    /** @var UserFormFactory */
    private $userFormFactory;

    /** @var Environment */
    private $twig;

    /**
     * @param UserManager     $userManager
     * @param UserFormFactory $userFormFactory
     * @param Environment     $twig
     */
    public function __construct(UserManager $userManager, UserFormFactory $userFormFactory, Environment $twig)
    {
        $this->userManager = $userManager;
        $this->userFormFactory = $userFormFactory;
        $this->twig = $twig;
    }

    /**
     * @throws \Twig_Error_Loader  When the template cannot be found
     * @throws \Twig_Error_Syntax  When an error occurred during compilation
     * @throws \Twig_Error_Runtime When an error occurred during rendering
     *
     * @return Response
     */
    public function __invoke(): Response
    {
        $users = $this->userManager->getAdministrableUsers();
        $deleteForms = $this->userFormFactory->createDeleteFormViews($users, 'khatovar_user_admin_remove');

        $content = $this->twig->render(
            'KhatovarUserBundle:Admin:index.html.twig',
            [
                'users' => $users,
                'delete_forms' => $deleteForms,
            ]
        );

        return new Response($content);
    }
}
