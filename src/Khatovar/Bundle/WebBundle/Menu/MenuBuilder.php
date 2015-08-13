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

use Khatovar\Bundle\ExactionBundle\Manager\ExactionManager;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

/**
 * Build the various application menus.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\Bundle\WebBundle\Menu
 */
class MenuBuilder
{
    /** @var array */
    protected $performances = array(
        'introduction' => 'Introduction',
        'combat'       => 'Combat',
        'campement'    => 'Vie de camp',
        'forge'        => 'Forge',
        'cuir'         => 'Cuir',
        'maille'       => 'Maille',
        'armes'        => 'Armes et armures',
        'herbo'        => 'Herboristerie et cuisine',
        'tissage'      => 'Tissage',
        'calligraphie' => 'Calligraphie'
    );

    /** @var array */
    protected $exactionYears;

    /** @var FactoryInterface */
    protected $menuFactory;

    /**
     * @param FactoryInterface $menuFactory
     * @param ExactionManager  $exactionManager
     */
    public function __construct(FactoryInterface $menuFactory, ExactionManager $exactionManager)
    {
        $this->menuFactory   = $menuFactory;
        $this->exactionYears = $exactionManager->getSortedYears();
    }

    /**
     * Main menu of the application.
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createMainMenu()
    {
        $menu = $this->menuFactory->createItem('root');
        $menu->setChildrenAttribute('id', 'menu');

        $this->addHome($menu);
        $this->addAppearances($menu);
        $this->addDates($menu);
        $this->addCamp($menu);
        $this->addContact($menu);

        return $menu;
    }

    /**
     * @param ItemInterface $menu
     */
    protected function addHome(ItemInterface $menu)
    {
        $menu->addChild(
            'home',
            array(
                'label' => 'Homepage',
                'route' => 'khatovar_web_homepage',
            )
        );
    }

    /**
     * @param ItemInterface $menu
     */
    protected function addAppearances(ItemInterface $menu)
    {
        $menu->addChild(
            'appearances',
            array(
                'label' => 'Nos prestations',
                'route' => 'khatovar_web_camp',
            )
        );

        foreach ($this->performances as $key => $name) {
            if ($key == 'combat') {
                $menu['appearances']->addChild(
                    $key,
                    array(
                        'label' => $name,
                        'route' => 'khatovar_web_fight',
                    )
                );
            } else {
                $menu['appearances']->addChild(
                    $key,
                    array(
                        'label' => $name,
                        'route' => 'khatovar_web_camp',
                        'routeParameters' => array('atelier' => $key)
                    )
                );
            }

        }
    }

    /**
     * @param ItemInterface $menu
     */
    protected function addDates(ItemInterface $menu)
    {
        $menu->addChild(
            'dates',
            array(
                'label' => 'Toutes nos dates',
                'route' => 'khatovar_web_homepage', // TODO: Créer une nouvelle route
            )
        );

        $menu['dates']->addChild(
            'schedule',
            array(
                'label' => 'À venir',
                'route' => 'khatovar_web_exaction_to_come',
            )
        );

        $menu['dates']->addChild(
            'references',
            array(
                'label' => 'Passées',
                'route' => 'khatovar_web_exaction_past',
            )
        );

        foreach ($this->exactionYears as $year) {
            $menu['dates']['references']->addChild(
                $year,
                array(
                    'label'           => 'Saison ' . $year,
                    'route'           => 'khatovar_web_exaction_list_by_year',
                    'routeParameters' => array('year' => $year),
                )
            );
        }
    }

    /**
     * @param ItemInterface $menu
     */
    protected function addCamp(ItemInterface $menu)
    {
        $menu->addChild(
            'camp',
            array(
                'label' => 'Le camp',
                'route' => 'khatovar_web_member',
            )
        );

        $menu['camp']->addChild(
            'camp_life',
            array(
                'label' => 'Vie de camp',
                'route' => 'khatovar_web_camp', // TODO: new controller? Rename the MemberBundle in CampBundle?
            )
        );

        $menu['camp']->addChild(
            'members',
            array(
                'label' => 'Les membres',
                'route' => 'khatovar_web_member',
            )
        );
    }

    /**
     * @param ItemInterface $menu
     */
    protected function addContact(ItemInterface $menu)
    {
        $menu->addChild(
            'links',
            array(
                'label' => 'Contact',
                'route' => 'khatovar_web_contact',
            )
        );
    }
}
