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

use Doctrine\ORM\EntityManagerInterface;
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
    /** @var \Khatovar\Bundle\AppearanceBundle\Entity\Appearance[] */
    protected $appearances;

    /** @var array */
    protected $exactionYears;

    /** @var FactoryInterface */
    protected $menuFactory;

    /** @var \Khatovar\Bundle\AppearanceBundle\Entity\Appearance[] */
    protected $programmes;

    /**
     * @param FactoryInterface       $menuFactory
     * @param ExactionManager        $exactionManager
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        FactoryInterface $menuFactory,
        EntityManagerInterface $entityManager,
        ExactionManager $exactionManager
    ) {
        $this->menuFactory    = $menuFactory;
        $appearanceRepository = $entityManager->getRepository('KhatovarAppearanceBundle:Appearance');

        $this->exactionYears = $exactionManager->getSortedYears();
        $this->appearances   = $appearanceRepository->findActiveAppearancesSortedBySlug();
        $this->programmes    = $appearanceRepository->findActiveProgrammesSortedBySlug();
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
            'programmes',
            array(
                'label' => 'Nos prestations',
                'route' => 'khatovar_web_appearance',
            )
        );

        foreach ($this->programmes as $programme) {
            $menu['programmes']->addChild(
                $programme->getSlug(),
                array(
                    'label'           => $programme->getName(),
                    'route'           => 'khatovar_web_appearance_show',
                    'routeParameters' => array('slug' => $programme->getSlug()),
                )
            );
        }

        $menu['programmes']->addChild(
            'appearances',
            array(
                'label' => 'Nos différents ateliers',
                'route' => 'khatovar_web_appearance_workshop',
            )
        );

        foreach ($this->appearances as $appearance) {
            $menu['programmes']['appearances']->addChild(
                $appearance->getSlug(),
                array(
                    'label'           => $appearance->getName(),
                    'route'           => 'khatovar_web_appearance_show',
                    'routeParameters' => array('slug' => $appearance->getSlug()),
                )
            );
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
                'route' => 'khatovar_web_exaction',
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
                'route' => 'khatovar_web_camp',
            )
        );

        $menu['camp']->addChild(
            'camp_life',
            array(
                'label' => 'Vie de camp',
                'route' => 'khatovar_web_appearance_camp',
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
