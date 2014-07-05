<?php
/**
 *
 * This file is part of Documents.
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
 *
 * @copyright   Copyright (C) Damien Carcel (https://github.com/damien-carcel)
 * @link        https://github.com/damien-carcel/Documents
 * @license     http://www.gnu.org/licenses/gpl.html
 */

namespace Carcel\UserBundle\Handler;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

/**
 * Class LogoutSuccessHandler
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Carcel\UserBundle\Handler
 */
class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    /**
     * Redirect user after logout to the last page he was.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function onLogoutSuccess(Request $request)
    {
        $url = $request->headers->get('referer');
        if (isset($url)) {
            return new RedirectResponse($url);
        }
        return new RedirectResponse('/');
    }
}
