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

namespace Khatovar\Component\User\Application\Command;

use Khatovar\Component\User\Domain\Repository\UserRepositoryInterface;

/**
 * Enables or disables a user.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserStatusHandler
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    /**
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param UserStatus $userStatus
     */
    public function handle(UserStatus $userStatus): void
    {
        $user = $userStatus->user();
        $status = $userStatus->status();

        $user->setEnabled($status);

        $this->userRepository->save($user);
    }
}
