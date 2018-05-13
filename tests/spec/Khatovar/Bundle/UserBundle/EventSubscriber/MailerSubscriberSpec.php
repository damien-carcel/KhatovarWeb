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

namespace spec\Khatovar\Bundle\UserBundle\EventSubscriber;

use Khatovar\Component\User\Domain\Model\UserInterface;
use Khatovar\Component\User\Domain\Event\UserEvents;
use Khatovar\Bundle\UserBundle\Factory\SwiftMessageFactory;
use PhpSpec\ObjectBehavior;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class MailerSubscriberSpec extends ObjectBehavior
{
    function let(
        \Swift_Mailer $mailer,
        TranslatorInterface $translator,
        SwiftMessageFactory $messageFactory
    ) {
        $this->beConstructedWith($mailer, $translator, $messageFactory, 'mailer@mail.address');
    }

    function it_subscribes_to_user_remove_events()
    {
        $this->getSubscribedEvents()->shouldReturn([
            UserEvents::POST_REMOVE => 'sendMessage',
        ]);
    }

    function it_sends_an_email_to_a_user(
        $mailer,
        $translator,
        $messageFactory,
        GenericEvent $event,
        UserInterface $user,
        \Swift_Message $message
    ) {
        $event->getSubject()->willReturn($user);

        $user->getEmail()->willReturn('user@mail.address');
        $user->getUsername()->willReturn('user_name');

        $translator->trans('khatovar_user.mail.remove.subject')->willReturn('A translated subject');
        $translator->trans(
            'khatovar_user.mail.remove.body',
            [
                '%username%' => 'user_name',
            ]
        )->willReturn('A translated body for "user_name"');

        $messageFactory->create(
            'mailer@mail.address',
            'user@mail.address',
            'A translated subject',
            'A translated body for "user_name"'
        )->willReturn($message);

        $mailer->send($message)->shouldBeCalled();

        $this->sendMessage($event);
    }

    function it_does_not_send_an_email_if_the_event_does_not_contains_a_user(
        GenericEvent $event,
        \StdClass $object
    ) {
        $event->getSubject()->willReturn($object);

        $this->sendMessage($event);
    }
}
