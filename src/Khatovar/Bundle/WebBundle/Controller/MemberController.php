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

namespace Khatovar\Bundle\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class MemberController extends Controller
{
    /**
     * Return the list of all members, both active or not.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $memberRepository = $this->get('doctrine')->getRepository('KhatovarWebBundle:Member');
        $activeMembers = $memberRepository->findBy(['active' => true]);
        $pastMembers = $memberRepository->findBy(['active' => false]);
        $activeDeleteForms = $this->createDeleteForms($activeMembers);
        $pastDeleteForms = $this->createDeleteForms($pastMembers);

        return $this->render(
            'KhatovarWebBundle:Member:index.html.twig',
            [
                'active_members' => $activeMembers,
                'past_members' => $pastMembers,
                'active_delete_forms' => $activeDeleteForms,
                'past_delete_forms' => $pastDeleteForms,
            ]
        );
    }

    /**
     * Finds and displays a member page.
     *
     * @param string $slug
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($slug)
    {
        $member = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Member')
            ->findBySlugOr404($slug);

        $currentUser = $this->getUser();

        $photos = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Photo')
            ->getAllButPortrait($member);

        return $this->render(
            'KhatovarWebBundle:Member:show.html.twig',
            [
                'member' => $member,
                'current_user' => $currentUser,
                'photos' => $photos,
            ]
        );
    }
}
