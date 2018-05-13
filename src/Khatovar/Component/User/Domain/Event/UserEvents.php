<?php

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

namespace Khatovar\Component\User\Domain\Event;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserEvents
{
    /**
     * This event is dispatched just before a user is saved.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent"
     */
    const PRE_SAVE = 'khatovar_user.event.pre_save';

    /**
     * This event is dispatched just after a user is saved.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent"
     */
    const POST_SAVE = 'khatovar_user.event.post_save';

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
