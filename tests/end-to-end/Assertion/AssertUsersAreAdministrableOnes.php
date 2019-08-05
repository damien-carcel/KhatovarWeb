<?php

declare(strict_types=1);

/*
 * This file is part of Khatovar.
 *
 * Copyright (c) 2019 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Khatovar\Tests\EndToEnd\Assertion;

use Khatovar\Component\User\Application\Query\GetAdministrableUsers;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
final class AssertUsersAreAdministrableOnes
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function __invoke(array $usernames): void
    {
        $storedUsers = $this->getAdministrableUsers()->forCurrentOne();

        $usernamesOfAdministrableUsers = [];
        foreach ($storedUsers as $storedUser) {
            $usernamesOfAdministrableUsers[] = $storedUser->getUsername();
        }
        sort($usernamesOfAdministrableUsers);

        Assert::same($usernames, $usernamesOfAdministrableUsers);
    }

    private function getAdministrableUsers(): GetAdministrableUsers
    {
        return $this->kernel->getContainer()->get(GetAdministrableUsers::class);
    }
}
