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

namespace Khatovar\Bundle\UserBundle\Factory;

/**
 * Creates a new instance of \Swift_Message.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class SwiftMessageFactory
{
    /**
     * @param string $fromAddress
     * @param string $toAddress
     * @param string $subject
     * @param string $body
     *
     * @return \Swift_Message
     */
    public function create(string $fromAddress, string $toAddress, string $subject, string $body): \Swift_Message
    {
        $message = new \Swift_Message();

        $message->setFrom($fromAddress);
        $message->setTo($toAddress);
        $message->setSubject($subject);
        $message->setBody($body);

        return $message;
    }
}
