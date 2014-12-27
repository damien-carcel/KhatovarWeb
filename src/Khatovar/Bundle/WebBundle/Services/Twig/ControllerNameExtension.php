<?php
/**
 *
 * This file is part of KhatovarWeb.
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
 * @link        https://github.com/damien-carcel/KhatovarWeb
 * @license     http://www.gnu.org/licenses/gpl.html
 */

namespace Khatovar\Bundle\WebBundle\Services\Twig;

use Symfony\Component\HttpFoundation\Request;

/**
 * An twig extension to return the name of the current controller and
 * the action executed.
 * Thanks to Dani Sancas for it : http://stackoverflow.com/a/17544023
 *
 * @package Khatovar\Bundle\WebBundle\Services\Twig
 */
class ControllerNameExtension extends \Twig_Extension
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @param Request $request
     */
    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * @param \Twig_Environment $environment
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'get_controller_name' => new \Twig_Function_Method($this, 'getControllerName'),
            'get_action_name' => new \Twig_Function_Method($this, 'getActionName'),
            'get_slug_or_id' => new \Twig_Function_Method($this, 'getSlugOrId')
        );
    }

    /**
     * Get current controller name.
     *
     * @return string|null
     */
    public function getControllerName()
    {
        $name = null;

        if (!is_null($this->request)) {
            $pattern = '#Controller\\\([a-zA-Z]*)Controller#';
            $matches = array();
            preg_match($pattern, $this->request->get('_controller'), $matches);

            $name = strtolower($matches[1]);
        }

        return $name;
    }

    /**
     * Get current action name.
     *
     * @return string|null
     */
    public function getActionName()
    {
        $name = null;

        if (!is_null($this->request)) {
            $pattern = "#::([a-zA-Z]*)Action#";
            $matches = array();
            preg_match($pattern, $this->request->get('_controller'), $matches);

            $name =  $matches[1];
        }

        return $name;
    }

    /**
     * Return the current slug or ID of the displayed object.
     *
     * @return string|null
     */
    public function getSlugOrId()
    {
        $slugOrId = null;

        if (!is_null($this->request)) {
            // First we check if there is a slug
            $slugOrId = $this->request->get('member_slug');
            // If there is not, it's possible we are in edition, then
            // We don't want a slug but an ID
            if (is_null($slugOrId)) {
                // Return an array containing one key/value, we want the value.
                $route_params = $this->request->get('_route_params');
                foreach ($route_params as $param) {
                    $slugOrId = (int) $param;
                }
            }
        }

        return $slugOrId;
    }

    /**
     * Return the name of the extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'controller_name_extension';
    }
}
