<?php

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Khatovar\Bundle\UserBundle\Manager;

use Khatovar\Bundle\UserBundle\Entity\Repository\UserRepositoryInterface;
use Khatovar\Bundle\UserBundle\Entity\UserInterface;
use Khatovar\Bundle\UserBundle\Manager\RolesManager;
use Khatovar\Bundle\UserBundle\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Khatovar\Bundle\UserBundle\Security\Core\Authentication\CurrentUser;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserManagerSpec extends ObjectBehavior
{
    function let(
        CurrentUser $currentUserInStorage,
        UserRepositoryInterface $userRepository,
        RegistryInterface $doctrine,
        RolesManager $rolesManager
    ) {
        $this->beConstructedWith($currentUserInStorage, $userRepository, $doctrine, $rolesManager);
    }

    function it_is_a_user_manager()
    {
        $this->shouldHaveType(UserManager::class);
    }

    function it_returns_all_users_but_the_current_one_and_the_super_admin(
        $currentUserInStorage,
        $userRepository,
        UserInterface $superAdmin,
        UserInterface $currentUser,
        UserInterface $regularUser
    ) {
        $currentUserInStorage->getFromTokenStorage()->willReturn($currentUser);
        $currentUserInStorage->isSuperAdmin()->willReturn(false);

        $userRepository->findByRole('ROLE_SUPER_ADMIN')->willReturn([$superAdmin]);
        $userRepository->findAllBut([$currentUser, $superAdmin])->willReturn([$regularUser]);

        $this->getAdministrableUsers()->shouldReturn([$regularUser]);
    }

    function it_returns_all_users_but_the_current_one_being_the_super_admin(
        $currentUserInStorage,
        UserRepositoryInterface $userRepository,
        UserInterface $regularAdmin,
        UserInterface $currentUser,
        UserInterface $regularUser
    ) {
        $currentUserInStorage->getFromTokenStorage()->willReturn($currentUser);

        $currentUser->isSuperAdmin()->willReturn(true);
        $userRepository->findByRole(Argument::any())->shouldNotBeCalled();
        $userRepository->findAllBut([$currentUser])->willReturn([$regularAdmin, $regularUser]);

        $this->getAdministrableUsers()->shouldReturn([$regularAdmin, $regularUser]);
    }

    function it_sets_role_to_a_user(
        $rolesManager,
        $doctrine,
        EntityManagerInterface $entityManager,
        UserInterface $user
    ) {
        $rolesManager->getChoices()->willReturn(
            [
                'ROLE_USER' => 'ROLE_USER',
                'ROLE_VIEWER' => 'ROLE_VIEWER',
                'ROLE_ADMIN' => 'ROLE_ADMIN',
            ]
        );

        $user->setRoles(['ROLE_ADMIN'])->shouldBeCalled();
        $doctrine->getManager()->willReturn($entityManager);
        $entityManager->flush()->shouldBeCalled();

        $this->setRole($user, ['roles' => 'ROLE_ADMIN']);
    }

    function it_throws_an_exception_if_role_is_not_in_choices_list(
        $rolesManager,
        $doctrine,
        EntityManagerInterface $entityManager,
        UserInterface $user
    ) {
        $rolesManager->getChoices()->willReturn(
            [
                'ROLE_USER' => 'ROLE_USER',
                'ROLE_VIEWER' => 'ROLE_VIEWER',
            ]
        );

        $user->setRoles([Argument::any()])->shouldNotBeCalled();
        $doctrine->getManager()->willReturn($entityManager);
        $entityManager->flush()->shouldNotBeCalled();

        $this
            ->shouldThrow(new InvalidArgumentException('Impossible to set role ROLE_ADMIN'))
            ->during('setRole', [$user, ['roles' => 'ROLE_ADMIN']]);
    }
}
