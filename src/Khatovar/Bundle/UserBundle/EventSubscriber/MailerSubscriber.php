<?php

declare(strict_types=1);

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
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

namespace Khatovar\Bundle\UserBundle\EventSubscriber;

use Khatovar\Bundle\UserBundle\Factory\SwiftMessageFactory;
use Khatovar\Component\User\Domain\Event\UserEvents;
use Khatovar\Component\User\Domain\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Subscriber that sends email to a user when its account has been removed.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class MailerSubscriber implements EventSubscriberInterface
{
    /** @var TranslatorInterface */
    protected $translator;

    /** @var \Swift_Mailer */
    private $mailer;

    /** @var SwiftMessageFactory */
    private $messageFactory;

    /** @var string */
    private $mailerAddress;

    /**
     * @param \Swift_Mailer       $mailer
     * @param TranslatorInterface $translator
     * @param SwiftMessageFactory $messageFactory
     * @param string              $mailerAddress
     */
    public function __construct(
        \Swift_Mailer $mailer,
        TranslatorInterface $translator,
        SwiftMessageFactory $messageFactory,
        $mailerAddress
    ) {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->messageFactory = $messageFactory;
        $this->mailerAddress = $mailerAddress;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            UserEvents::POST_REMOVE => 'sendMessage',
        ];
    }

    /**
     * Sends an email to the removed user.
     *
     * @param GenericEvent $event
     */
    public function sendMessage(GenericEvent $event)
    {
        $user = $event->getSubject();

        if (!$user instanceof UserInterface) {
            return;
        }

        $subject = $this->translator->trans('khatovar_user.mail.remove.subject');
        $body = $this->translator->trans(
            'khatovar_user.mail.remove.body',
            [
                '%username%' => $user->getUsername(),
            ]
        );

        $message = $this->messageFactory->create(
            $this->mailerAddress,
            $user->getEmail(),
            $subject,
            $body
        );

        $this->mailer->send($message);
    }
}
