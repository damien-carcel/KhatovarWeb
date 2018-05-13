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

namespace Khatovar\Bundle\UserBundle\EventSubscriber;

use Khatovar\Bundle\UserBundle\Entity\UserInterface;
use Khatovar\Bundle\UserBundle\Event\UserEvents;
use Khatovar\Bundle\UserBundle\Factory\SwiftMessageFactory;
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
