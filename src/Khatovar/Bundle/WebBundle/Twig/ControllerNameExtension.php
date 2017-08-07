<?php

/**
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2015 Damien Carcel (https://github.com/damien-carcel)
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
 */

namespace Khatovar\Bundle\WebBundle\Twig;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 *
 * Twig extension that return the name of the current controller and
 * the action executed.
 * Thanks to Dani Sancas for it: http://stackoverflow.com/a/17544023.
 */
class ControllerNameExtension extends \Twig_Extension
{
    /** @var Request */
    protected $request;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            'get_controller_name' => new \Twig_SimpleFunction(
                'controller_name_extension',
                [$this, 'getControllerName']
            ),
            'get_action_name' => new \Twig_SimpleFunction(
                'controller_name_extension',
                [$this, 'getActionName']
            ),
            'get_slug_or_id' => new \Twig_SimpleFunction(
                'controller_name_extension',
                [$this, 'getSlugOrId']
            ),
        ];
    }

    /**
     * Get current controller name.
     *
     * @return string|null
     */
    public function getControllerName()
    {
        $name = null;

        if (null !== $this->request) {
            $pattern = '#.([a-zA-Z]*):#';
            preg_match($pattern, $this->request->get('_controller'), $matches);

            $name = strtolower($matches[1]);
        }

        return str_replace('controller', '', $name);
    }

    /**
     * Get current action name.
     *
     * @return string|null
     */
    public function getActionName()
    {
        $name = null;

        if (null !== $this->request) {
            $pattern = '#:([a-zA-Z]*)Action#';
            $matches = [];
            preg_match($pattern, $this->request->get('_controller'), $matches);

            $name = $matches[1];
        }

        return $name;
    }

    /**
     * Return the current slug or ID of the displayed object.
     *
     * @return int|string|null
     */
    public function getSlugOrId()
    {
        $slugOrId = $this->request->get('slug');

        if (null === $slugOrId) {
            $slugOrId = (int) $this->request->get('id');
        }

        return $slugOrId;
    }
}
