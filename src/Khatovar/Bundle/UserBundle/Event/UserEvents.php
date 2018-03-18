<?php

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Khatovar\Bundle\UserBundle\Event;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserEvents
{
    /**
     * This event is dispatched just before a user is updated.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent"
     */
    const PRE_UPDATE = 'khatovar_user.event.pre_update';

    /**
     * This event is dispatched just after a user is updated.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent"
     */
    const POST_UPDATE = 'khatovar_user.event.post_update';

    /**
     * This event is dispatched just before a user is removed.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent"
     */
    const PRE_REMOVE = 'khatovar_user.event.pre_remove';

    /**
     * This event is dispatched just after a user is removed.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent"
     */
    const POST_REMOVE = 'khatovar_user.event.post_remove';

    /**
     * This event is dispatched just before the role of a user is changed.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent"
     */
    const PRE_SET_ROLE = 'khatovar_user.event.pre_set_role';

    /**
     * This event is dispatched just after the role of a user is changed.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent"
     */
    const POST_SET_ROLE = 'khatovar_user.event.post_set_role';

    /**
     * This event is dispatched just after a user is activated.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent"
     */
    const PRE_ACTIVATE = 'khatovar_user.event.pre_activate';

    /**
     * This event is dispatched just after a user is activated.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent"
     */
    const POST_ACTIVATE = 'khatovar_user.event.post_activate';

    /**
     * This event is dispatched just after a user is deactivated.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent"
     */
    const PRE_DEACTIVATE = 'khatovar_user.event.pre_deactivate';

    /**
     * This event is dispatched just after a user is deactivated.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent"
     */
    const POST_DEACTIVATE = 'khatovar_user.event.post_deactivate';
}
