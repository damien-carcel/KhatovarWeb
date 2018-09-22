<?php

declare(strict_types=1);

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

namespace Khatovar\Bundle\WebBundle\Menu;

use Khatovar\Bundle\WebBundle\Entity\AppearanceRepository;
use Khatovar\Bundle\WebBundle\Manager\ExactionManager;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

/**
 * Build the web site menu.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class MenuBuilder
{
    /** @var ExactionManager */
    private $exactionManager;

    /** @var FactoryInterface */
    private $menuFactory;

    /** @var AppearanceRepository */
    private $appearanceRepository;

    /**
     * @param FactoryInterface     $menuFactory
     * @param AppearanceRepository $appearanceRepository
     * @param ExactionManager      $exactionManager
     */
    public function __construct(
        FactoryInterface $menuFactory,
        AppearanceRepository $appearanceRepository,
        ExactionManager $exactionManager
    ) {
        $this->menuFactory = $menuFactory;
        $this->appearanceRepository = $appearanceRepository;
        $this->exactionManager = $exactionManager;
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
    private function addHome(ItemInterface $menu): void
    {
        $menu->addChild(
            'home',
            [
                'label' => 'Accueil',
                'route' => 'khatovar_web_homepage',
            ]
        );
    }

    /**
     * @param ItemInterface $menu
     */
    private function addAppearances(ItemInterface $menu): void
    {
        $menu->addChild(
            'programmes',
            [
                'label' => 'Nos prestations',
                'route' => 'khatovar_web_appearance',
            ]
        );

        $programmes = $this->appearanceRepository->findActiveProgrammesSortedBySlug();
        foreach ($programmes as $programme) {
            $menu['programmes']->addChild(
                $programme->getSlug(),
                [
                    'label' => $programme->getName(),
                    'route' => 'khatovar_web_appearance_show',
                    'routeParameters' => ['slug' => $programme->getSlug()],
                ]
            );
        }

        $menu['programmes']->addChild(
            'appearances',
            [
                'label' => 'Nos différents ateliers',
                'route' => 'khatovar_web_appearance_workshop',
            ]
        );

        $appearances = $this->appearanceRepository->findActiveWorkshopsSortedBySlug();
        foreach ($appearances as $appearance) {
            $menu['programmes']['appearances']->addChild(
                $appearance->getSlug(),
                [
                    'label' => $appearance->getName(),
                    'route' => 'khatovar_web_appearance_show',
                    'routeParameters' => ['slug' => $appearance->getSlug()],
                ]
            );
        }
    }

    /**
     * @param ItemInterface $menu
     */
    private function addDates(ItemInterface $menu): void
    {
        $menu->addChild(
            'dates',
            [
                'label' => 'Toutes nos dates',
                'route' => 'khatovar_web_exaction',
            ]
        );

        $menu['dates']->addChild(
            'schedule',
            [
                'label' => 'À venir',
                'route' => 'khatovar_web_exaction_to_come',
            ]
        );

        $menu['dates']->addChild(
            'references',
            [
                'label' => 'Passées',
                'route' => 'khatovar_web_exaction_past',
            ]
        );

        $sortedYears = $this->exactionManager->getSortedYears();
        foreach ($sortedYears as $year) {
            $menu['dates']['references']->addChild(
                $year,
                [
                    'label' => 'Saison '.$year,
                    'route' => 'khatovar_web_exaction_list_by_year',
                    'routeParameters' => ['year' => $year],
                ]
            );
        }
    }

    /**
     * @param ItemInterface $menu
     */
    private function addCamp(ItemInterface $menu): void
    {
        $menu->addChild(
            'camp',
            [
                'label' => 'Le camp',
                'route' => 'khatovar_web_camp',
            ]
        );

        $menu['camp']->addChild(
            'camp_life',
            [
                'label' => 'Vie de camp',
                'route' => 'khatovar_web_appearance_camp',
            ]
        );

        $menu['camp']->addChild(
            'members',
            [
                'label' => 'Les membres',
                'route' => 'khatovar_web_member',
            ]
        );
    }

    /**
     * @param ItemInterface $menu
     */
    private function addContact(ItemInterface $menu): void
    {
        $menu->addChild(
            'links',
            [
                'label' => 'Contact',
                'route' => 'khatovar_web_contact',
            ]
        );
    }
}
