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

namespace spec\Khatovar\Tests\Acceptance\Repository\InMemory;

use Khatovar\Bundle\UserBundle\Entity\User;
use Khatovar\Component\User\Domain\Exception\UserDoesNotExist;
use Khatovar\Component\User\Domain\Model\UserInterface;
use Khatovar\Component\User\Domain\Repository\UserRepositoryInterface;
use Khatovar\Tests\Acceptance\Repository\InMemory\UserRepository;
use PhpSpec\ObjectBehavior;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserRepositorySpec extends ObjectBehavior
{
    /** @var UserInterface[] */
    private $users = [];

    function let()
    {
        $this->addUsers([
            [
                'username' => 'admin',
                'role' => 'ROLE_SUPER_ADMIN',
            ],
            [
                'username' => 'aurore',
                'role' => 'ROLE_ADMIN',
            ],
            [
                'username' => 'damien',
                'role' => 'ROLE_ADMIN',
            ],
            [
                'username' => 'freya',
                'role' => 'ROLE_UPLOADER',
            ],
            [
                'username' => 'lilith',
                'role' => 'ROLE_VIEWER',
            ],
        ]);

        $this->beConstructedWith($this->users);
    }

    function it_is_an_in_memory_user_repository()
    {
        $this->shouldHaveType(UserRepository::class);
        $this->shouldImplement(UserRepositoryInterface::class);
    }

    function it_finds_all_but_a_list_of_users()
    {
        $this->findAllBut([
            $this->users['admin'],
            $this->users['lilith']
        ])->shouldReturn([
            'aurore' => $this->users['aurore'],
            'damien' => $this->users['damien'],
            'freya' => $this->users['freya'],
        ]);
    }

    function it_finds_a_user_by_its_role()
    {
        $this->findByRole('ROLE_VIEWER')->shouldReturn([
            'lilith' => $this->users['lilith'],
        ]);
    }

    function it_finds_several_users_with_the_same_role()
    {
        $this->findByRole('ROLE_ADMIN')->shouldReturn([
            'aurore' => $this->users['aurore'],
            'damien' => $this->users['damien'],
        ]);
    }

    function it_finds_no_users_for_a_non_existing_role()
    {
        $this->findByRole('ROLE_INVENTED')->shouldReturn([]);
    }

    function it_gets_a_user_from_its_username()
    {
        $this->get('aurore')->shouldReturn($this->users['aurore']);
    }

    function it_throws_an_exception_if_user_does_not_exists()
    {
        $this->shouldThrow(new UserDoesNotExist('basile'))->during('get', ['basile']);
    }

    function it_saves_a_new_user()
    {
        $user = $this->createUser([
            'username' => 'pandore',
            'role' => 'ROLE_VIEWER',
        ]);

        $this->save($user);

        $this->shouldHaveUserCount(6);
        $this->shouldHaveUser('pandore');
    }

    function it_updates_an_existing_user()
    {
        $user = $this->createUser([
            'username' => 'lilith',
            'role' => 'ROLE_EDITOR',
        ]);

        $this->save($user);

        $this->shouldHaveUserCount(5);
        $this->shouldHaveRoleForUser('ROLE_EDITOR', 'lilith');
    }

    function it_removes_a_user()
    {
        $this->remove($this->users['damien']);

        $this->shouldHaveUserCount(4);
        $this->shouldNotHaveUser('damien');
    }

    /**
     * @return array
     */
    public function getMatchers(): array
    {
        return [
            'haveUserCount' => function (UserRepository $repository, int $userCount) {
                return $userCount === $repository->users->count();
            },
            'haveUser' => function (UserRepository $repository, string $username) {
                foreach ($repository->users as $user) {
                    if ($username === $user->getUsername()) {
                        return true;
                    }
                }

                return false;
            },
            'haveRoleForUser' => function (UserRepository $repository, string $role, string $username) {
                return in_array(
                    $role,
                    $repository->users->get($username)->getRoles()
                );
            },
            'notHaveUser' => function (UserRepository $repository, string $username) {
                return null === $repository->users->get($username);
            },
        ];
    }

    /**
     * @param array $usersData
     */
    private function addUsers(array $usersData): void
    {
        foreach ($usersData as $userData) {
            $user = $this->createUser($userData);
            $this->users[$userData['username']] = $user;
        }
    }

    /**
     * @param array $userData
     *
     * @return UserInterface
     */
    private function createUser(array $userData): UserInterface
    {
        $user = new User();
        $user->setUsername($userData['username']);
        $user->addRole($userData['role']);

        return $user;
    }
}
