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

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Webmozart\Assert\Assert;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
final class AssertAuthenticatedAsUser
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function __invoke(string $username): void
    {
        if (null === $token = $this->tokenStorage()->getToken()) {
            throw new TokenNotFoundException();
        }

        Assert::same($token->getUsername(), $username);
    }

    private function tokenStorage(): TokenStorageInterface
    {
        return $this->kernel->getContainer()->get('security.token_storage');
    }
}
