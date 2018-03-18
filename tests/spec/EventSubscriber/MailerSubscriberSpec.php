<?php

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Khatovar\Bundle\UserBundle\EventSubscriber;

use Khatovar\Bundle\UserBundle\Event\UserEvents;
use Khatovar\Bundle\UserBundle\EventSubscriber\MailerSubscriber;
use Khatovar\Bundle\UserBundle\Manager\MailManager;
use FOS\UserBundle\Model\UserInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class MailerSubscriberSpec extends ObjectBehavior
{
    function let(MailManager $mailManager)
    {
        $this->beConstructedWith($mailManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MailerSubscriber::class);
    }

    function it_is_an_event_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_subscribes_to_user_remove_events()
    {
        $this->getSubscribedEvents()->shouldReturn([
            UserEvents::PRE_REMOVE  => 'getUserEmail',
            UserEvents::POST_REMOVE => 'sendMessage',
        ]);
    }

    function it_gets_user_mail_address_and_username(
        GenericEvent $event,
        UserInterface $user
    ) {
        $event->getSubject()->willReturn($user);

        $user->getEmail()->shouldBeCalled();
        $user->getUsername()->shouldBeCalled();

        $this->getUserEmail($event);
    }

    function it_throws_an_exception_if_subject_is_not_a_user(GenericEvent $event)
    {
        $event->getSubject()->willReturn('foobar');

        $this->shouldThrow(
            new \InvalidArgumentException('MailerSubscriber event is expected to contain an instance of User')
        )->during('getUserEmail', [$event]);
    }

    function it_sends_an_email_to_a_user($mailManager)
    {
        $mailManager->send(
            null,
            null,
            'khatovar_user.mail.remove.subject',
            'khatovar_user.mail.remove.body'
        )->shouldBeCalled();

        $this->sendMessage();
    }
}
