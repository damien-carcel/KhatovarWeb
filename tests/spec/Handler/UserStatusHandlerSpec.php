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

use Khatovar\Bundle\UserBundle\Event\UserEvents;
use Khatovar\Bundle\UserBundle\Handler\UserStatusHandler;
use Khatovar\Bundle\UserBundle\Handler\UserStatusHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserStatusHandlerSpec extends ObjectBehavior
{
    function let(EventDispatcherInterface $eventDispatcher, EntityManagerInterface $entityManager)
    {
        $this->beConstructedWith($eventDispatcher, $entityManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UserStatusHandler::class);
    }

    function it_is_a_user_status_handler()
    {
        $this->shouldImplement(UserStatusHandlerInterface::class);
    }

    function it_enables_a_user($eventDispatcher, $entityManager, UserInterface $user)
    {
        $eventDispatcher->dispatch(UserEvents::PRE_ACTIVATE, Argument::any())->shouldBeCalled();
        $eventDispatcher->dispatch(UserEvents::POST_ACTIVATE, Argument::any())->shouldBeCalled();

        $user->setEnabled(true)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->enable($user);
    }

    function it_disables_a_user($eventDispatcher, $entityManager, UserInterface $user)
    {
        $eventDispatcher->dispatch(UserEvents::PRE_DEACTIVATE, Argument::any())->shouldBeCalled();
        $eventDispatcher->dispatch(UserEvents::POST_DEACTIVATE, Argument::any())->shouldBeCalled();

        $user->setEnabled(false)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->disable($user);
    }
}
