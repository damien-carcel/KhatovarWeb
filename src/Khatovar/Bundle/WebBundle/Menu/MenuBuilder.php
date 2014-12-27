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

namespace Khatovar\Bundle\WebBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Build the various application menus.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\Bundle\WebBundle\Menu
 */
class MenuBuilder
{
    private $performances = array(
        'introduction' => 'Introduction',
        'combat' => 'Combat',
        'campement' => 'Vie de camp',
        'forge' => 'Forge',
        'cuir' => 'Cuir',
        'maille' => 'Maille',
        'armes' => 'Armes et armures',
        'herbo' => 'Herboristerie et cuisine',
        'tissage' => 'Tissage',
        'calligraphie' => 'Calligraphie'
    );

    private $contacts = array(
        'contact' => 'Qui sommes-nous ?',
        'allies' => 'Nos alliés',
        'fournisseurs' => 'Nos fournisseurs'
    );

    /**
     * @var FactoryInterface
     */
    private $factoryInterface;

    /**
     * Create an instance of MenuBuilder.
     *
     * @param FactoryInterface $factoryInterface
     */
    public function __construct(FactoryInterface $factoryInterface)
    {
        $this->factoryInterface = $factoryInterface;
    }

    /**
     * Main menu of the application.
     *
     * @param Request $request
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createMainMenu(Request $request)
    {
        $menu = $this->factoryInterface->createItem('root');

        $menu->addChild(
            'home',
            array('label' => 'Homepage', 'route' => 'khatovar_web_homepage')
        );

        $menu->addChild(
            'camp',
            array('label' => 'Nos prestations', 'route' => 'khatovar_web_camp')
        );
        foreach ($this->performances as $key => $name) {
            if ($key == 'combat') {
                $menu['camp']->addChild(
                    $key,
                    array(
                        'label' => $name,
                        'route' => 'khatovar_web_fight',
                    )
                );
            } else {
                $menu['camp']->addChild(
                    $key,
                    array(
                        'label' => $name,
                        'route' => 'khatovar_web_camp',
                        'routeParameters' => array('atelier' => $key)
                    )
                );
            }
        }

        $menu->addChild(
            'schedule',
            array(
                'label' => 'Exactions à venir',
                'route' => 'khatovar_web_schedule'
            )
        );

        $menu->addChild(
            'references',
            array(
                'label' => 'Exactions passées',
                'route' => 'khatovar_web_references'
            )
        );
        foreach (range(date('Y'), 2009) as $ref) {
            $menu['references']->addChild(
                $ref,
                array(
                    'label' => 'Saison ' . $ref,
                    'route' => 'khatovar_web_references',
                    'routeParameters' => array('year' => $ref)
                )
            );
        }

        $menu->addChild(
            'members',
            array('label' => 'Les membres', 'route' => 'khatovar_web_members')
        );

        $menu->addChild(
            'links',
            array('label' => 'Contacts', 'route' => 'khatovar_web_links')
        );
        foreach ($this->contacts as $key => $name) {
            $menu['links']->addChild(
                $key,
                array(
                    'label' => $name,
                    'route' => 'khatovar_web_links',
                    'routeParameters' => array('contact' => $key)
                )
            );
        }

        return $menu;
    }
}
