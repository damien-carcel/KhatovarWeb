<?php

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Khatovar\Bundle\UserBundle\Handler;

use Khatovar\Component\User\Domain\Event\UserEvents;
use Khatovar\Component\User\Domain\Model\UserInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserStatusHandler
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var RegistryInterface */
    protected $doctrine;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param RegistryInterface        $doctrine
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, RegistryInterface $doctrine)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->doctrine = $doctrine;
    }

    /**
     * Enables a user.
     *
     * @param UserInterface $user
     */
    public function enable(UserInterface $user)
    {
        $this->eventDispatcher->dispatch(UserEvents::PRE_ACTIVATE, new GenericEvent($user));

        $user->setEnabled(true);

        $this->doctrine->getManager()->flush();

        $this->eventDispatcher->dispatch(UserEvents::POST_ACTIVATE, new GenericEvent($user));
    }

    /**
     * Disables a user.
     *
     * @param UserInterface $user
     */
    public function disable(UserInterface $user)
    {
        $this->eventDispatcher->dispatch(UserEvents::PRE_DEACTIVATE, new GenericEvent($user));

        $user->setEnabled(false);

        $this->doctrine->getManager()->flush();

        $this->eventDispatcher->dispatch(UserEvents::POST_DEACTIVATE, new GenericEvent($user));
    }
}
