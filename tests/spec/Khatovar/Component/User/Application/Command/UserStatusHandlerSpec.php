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

namespace spec\Khatovar\Component\User\Application\Command;

use Khatovar\Component\User\Application\Command\UserStatus;
use Khatovar\Component\User\Application\Command\UserStatusHandler;
use Khatovar\Component\User\Domain\Model\UserInterface;
use Khatovar\Component\User\Domain\Repository\UserRepositoryInterface;
use PhpSpec\ObjectBehavior;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserStatusHandlerSpec extends ObjectBehavior
{
    function let(UserRepositoryInterface $userRepository)
    {
        $this->beConstructedWith($userRepository);
    }

    function it_is_a_user_status_handler()
    {
        $this->shouldHaveType(UserStatusHandler::class);
    }

    function it_enables_a_user($userRepository, UserInterface $user)
    {
        $userStatus = new UserStatus($user->getWrappedObject(), true);

        $user->setEnabled(true)->shouldBeCalled();
        $userRepository->save($user)->shouldBeCalled();

        $this->handle($userStatus);
    }

    function it_disables_a_user($userRepository, UserInterface $user)
    {
        $userStatus = new UserStatus($user->getWrappedObject(), false);

        $user->setEnabled(false)->shouldBeCalled();
        $userRepository->save($user)->shouldBeCalled();

        $this->handle($userStatus);
    }
}
