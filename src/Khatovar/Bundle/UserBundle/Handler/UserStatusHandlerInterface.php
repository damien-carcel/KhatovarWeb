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

use FOS\UserBundle\Model\UserInterface;

/**
 * Handles a user status enabled/disabled.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
interface UserStatusHandlerInterface
{
    /**
     * Enables a user.
     *
     * @param UserInterface $user
     */
    public function enable(UserInterface $user);

    /**
     * Disables a user.
     *
     * @param UserInterface $user
     */
    public function disable(UserInterface $user);
}
