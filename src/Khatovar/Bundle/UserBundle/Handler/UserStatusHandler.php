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

use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use Khatovar\Bundle\UserBundle\Event\UserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserStatusHandler implements UserStatusHandlerInterface
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param EntityManagerInterface   $entityManager
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, EntityManagerInterface $entityManager)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function enable(UserInterface $user)
    {
        $this->eventDispatcher->dispatch(UserEvents::PRE_ACTIVATE, new GenericEvent($user));

        $user->setEnabled(true);

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(UserEvents::POST_ACTIVATE, new GenericEvent($user));
    }

    /**
     * {@inheritdoc}
     */
    public function disable(UserInterface $user)
    {
        $this->eventDispatcher->dispatch(UserEvents::PRE_DEACTIVATE, new GenericEvent($user));

        $user->setEnabled(false);

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(UserEvents::POST_DEACTIVATE, new GenericEvent($user));
    }
}
