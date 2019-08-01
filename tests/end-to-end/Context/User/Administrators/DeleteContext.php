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

namespace Khatovar\Tests\EndToEnd\Context\User\Administrators;

use Khatovar\Tests\EndToEnd\Context\User\UserRawContext;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
final class DeleteContext extends UserRawContext
{
    /**
     * @When I remove the user :username
     */
    public function iRemoveTheUser(string $username): void
    {
        $this->pressActionButtonForUserRow('Supprimer', $username);
    }

    /**
     * @Then I should be noticed that user :username was removed
     */
    public function userWasRemoved(string $username): void
    {
        $this->assertPageContainsText('L\'utilisateur a bien été effacé');
    }
}
