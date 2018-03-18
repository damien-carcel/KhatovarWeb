<?php

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Khatovar\Bundle\UserBundle\Manager;

use Khatovar\Bundle\UserBundle\Factory\SwiftMessageFactory;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Mail manager.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class MailManager
{
    /** @var \Swift_Mailer */
    protected $mailer;

    /** @var string */
    protected $mailerAddress;

    /** @var SwiftMessageFactory */
    protected $messageFactory;

    /** @var TranslatorInterface */
    protected $translator;

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
     * Sends an email.
     *
     * @param string $mailAddress
     * @param string $username
     * @param string $subject
     * @param string $body
     *
     * @return int The number of successful recipients. Can be 0 which indicates failure
     */
    public function send($mailAddress, $username, $subject, $body)
    {
        $subject = $this->translator->trans($subject);
        $body = $this->translator->trans(
            $body,
            ['%username%' => $username]
        );

        $message = $this->messageFactory->create();
        $message
            ->setSubject($subject)
            ->setFrom($this->mailerAddress)
            ->setTo($mailAddress)
            ->setBody($body);

        return $this->mailer->send($message);
    }
}
