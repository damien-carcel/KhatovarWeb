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

use Symfony\Component\Routing\RouterInterface;

/**
 * Twig extension that normalizes links written in appearances content.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class AppearanceLinkNormalizer extends \Twig_Extension
{
    /** @var RouterInterface */
    protected $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter(
                'khatovar_normalize_appearance_links',
                [$this, 'formatText'],
                ['is_sage' => ['html']]
            ),
        ];
    }

    /**
     * Format html links in a text, given as:
     * [route|text to display].
     *
     * @param string $text
     *
     * @return string
     */
    public function formatText($text)
    {
        $formattedLinks = [];

        $links = $this->getLinks($text);

        foreach ($links as $link) {
            $formattedLinks[] = $this->formatLink($link);
        }

        return str_replace($links, $formattedLinks, $text);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'khatovar_appearance_link_normalizer';
    }

    /**
     * @param string $text
     *
     * @return array
     */
    protected function getLinks($text)
    {
        preg_match_all('#\[(.*?)\]#', $text, $matches);

        return $matches[0];
    }

    /**
     * @param $link
     *
     * @return string
     */
    protected function formatLink($link)
    {
        $format = '<a href="%s" title="%s">%s</a>';

        $explodedLink = explode('|', substr($link, 1, -1));

        $href = $this->router->generate(
            'khatovar_web_appearance_show',
            ['slug' => $explodedLink[0]]
        );

        return sprintf($format, $href, $explodedLink[1], $explodedLink[1]);
    }
}
