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

namespace Khatovar\Bundle\WebBundle\Twig;

use Khatovar\Bundle\PhotoBundle\Manager\PhotoManager;

/**
 * Class SidePanelExtension
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class SidePanelExtension extends \Twig_Extension
{
    /** @var PhotoManager */
    protected $photoManager;

    /**
     * @param PhotoManager $photoManager
     */
    public function __construct(PhotoManager $photoManager)
    {
        $this->photoManager = $photoManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'get_controller_photos' => new \Twig_Function_Method($this, 'getControllerPhotos'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'side_panel_extension';
    }

    /**
     * Display a list of all photos uploaded for the current page in a
     * small sidebar. Editors and admin can access all photos, but
     * regular users can only access photos of their own member page.
     *
     * @param string     $controller The controller currently rendered.
     * @param string     $action     The controller method used for rendering.
     * @param string|int $slugOrId   The slug or the ID of the object currently rendered.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getControllerPhotos($controller, $action, $slugOrId)
    {
        return $this->photoManager->getControllerPhotos($controller, $action, $slugOrId);
    }
}
