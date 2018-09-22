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

namespace Khatovar\Bundle\WebBundle\Twig;

/**
 * Twig extension that normalizes links stored in the Exaction entity.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class ExactionLinkNormalizer extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'khatovar_normalize_link',
                [$this, 'normalize'],
                ['is_sage' => ['html']]
            ),
        ];
    }

    /**
     * Returns an html link.
     *
     * @param string $link
     *
     * @return string
     */
    public function normalize($link)
    {
        $explodedLink = explode('|', $link);

        if (3 === count($explodedLink)) {
            $formattedLink = $this->formatLink($explodedLink[1], $explodedLink[0], $explodedLink[2]);
        } elseif (2 === count($explodedLink)) {
            $formattedLink = $this->formatLink($explodedLink[1], $explodedLink[0]);
        } else {
            $formattedLink = $this->formatLink($explodedLink[0], $explodedLink[0]);
        }

        return $formattedLink;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'khatovar_link_normalizer';
    }

    /**
     * Formats a web link.
     *
     * @param string $link
     * @param string $text
     * @param string $title
     *
     * @return string
     */
    protected function formatLink($link, $text, $title = '')
    {
        $format = '<a href="http://%s" title="%s">%s</a>';

        return sprintf($format, $link, $title, $text);
    }
}
