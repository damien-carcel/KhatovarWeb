<?php

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Khatovar\Bundle\UserBundle\Factory;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Creates a new instance of RedirectResponse.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class RedirectResponseFactory
{
    /**
     * Returns a redirect response for a target URL.
     *
     * @param string $url
     *
     * @return RedirectResponse
     */
    public function create($url)
    {
        return new RedirectResponse($url);
    }
}
