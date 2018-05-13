<?php

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Khatovar\Bundle\UserBundle\Handler;

use Khatovar\Component\User\Domain\Model\UserInterface;
use Khatovar\Component\User\Domain\Event\UserEvents;
use Khatovar\Bundle\UserBundle\Handler\UserStatusHandler;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserStatusHandlerSpec extends ObjectBehavior
{
    function let(EventDispatcherInterface $eventDispatcher, RegistryInterface $doctrine)
    {
        $this->beConstructedWith($eventDispatcher, $doctrine);
    }

    function it_is_a_user_status_handler()
    {
        $this->shouldHaveType(UserStatusHandler::class);
    }

    function it_enables_a_user(
        $eventDispatcher,
        $doctrine,
        EntityManagerInterface $entityManager,
        UserInterface $user
    ) {
        $eventDispatcher->dispatch(UserEvents::PRE_ACTIVATE, Argument::any())->shouldBeCalled();
        $eventDispatcher->dispatch(UserEvents::POST_ACTIVATE, Argument::any())->shouldBeCalled();

        $user->setEnabled(true)->shouldBeCalled();
        $doctrine->getManager()->willReturn($entityManager);
        $entityManager->flush()->shouldBeCalled();

        $this->enable($user);
    }

    function it_disables_a_user(
        $eventDispatcher,
        $doctrine,
        EntityManagerInterface $entityManager,
        UserInterface $user
    ) {
        $eventDispatcher->dispatch(UserEvents::PRE_DEACTIVATE, Argument::any())->shouldBeCalled();
        $eventDispatcher->dispatch(UserEvents::POST_DEACTIVATE, Argument::any())->shouldBeCalled();

        $user->setEnabled(false)->shouldBeCalled();
        $doctrine->getManager()->willReturn($entityManager);
        $entityManager->flush()->shouldBeCalled();

        $this->disable($user);
    }
}
