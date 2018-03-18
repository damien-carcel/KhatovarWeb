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

use Khatovar\Bundle\UserBundle\Factory\RedirectResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

/**
 * Redirect user after logout to the last page he was.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    /** @var RedirectResponseFactory */
    protected $factory;

    /**
     * @param RedirectResponseFactory $factory
     */
    public function __construct(RedirectResponseFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function onLogoutSuccess(Request $request)
    {
        $url = $request->headers->get('referer');

        if (null !== $url) {
            return $this->factory->create($url);
        }

        return $this->factory->create('/');
    }
}
