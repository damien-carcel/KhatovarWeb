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

namespace Khatovar\Tests\EndToEnd\Context\User;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
final class CommonContext extends UserRawContext
{
    /**
     * @When I get back to the previous page
     */
    public function backToThePreviousPage(): void
    {
        $this->page()->clickLink('Retour');
    }

    /**
     * @Then I should be on the login page
     */
    public function shouldBeOnTheLoginPage(): void
    {
        $this->assertPath('login');
    }
}
