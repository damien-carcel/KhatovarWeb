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

namespace Khatovar\Tests\EndToEnd\Service\Assert;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Webmozart\Assert\Assert;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
final class AssertUserIsAnonymous
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function __invoke(): void
    {
        Assert::true($this->authorizationChecker()->isGranted('IS_AUTHENTICATED_ANONYMOUSLY'));
        Assert::false($this->authorizationChecker()->isGranted('ROLE_USER'));
    }

    private function authorizationChecker(): AuthorizationCheckerInterface
    {
        return $this->kernel->getContainer()->get('security.authorization_checker');
    }
}
