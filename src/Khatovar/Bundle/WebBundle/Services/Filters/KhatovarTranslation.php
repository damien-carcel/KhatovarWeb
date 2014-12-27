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

namespace Khatovar\Bundle\WebBundle\Services\Filters;

use Doctrine\ORM\EntityManager;
use Khatovar\Bundle\PhotoBundle\Entity\Photo;

/**
 * Perform some transformations on html code before display or saving.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\Bundle\WebBundle\Services\Translation
 */
class KhatovarTranslation
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Look for special photo insertion tags and transform it in html syntax.
     *
     * @param string $text The text to transform.
     * @return string
     */
    public function imageTranslate($text)
    {
        // If the photo is inserted inside a paragraph instead of an empty div
        $text = str_replace('<p>[', '<div>[', $text);
        $text = str_replace(']</p>', ']</div>', $text);

        // Add the container class to the div
        $text = str_replace('<div>[', '<div class="container">', $text);
        $text = str_replace(']</div>', '</div>', $text);
        $text = str_replace('][', '', $text);

        // And finally create the html code. First we get all the photo
        // codes inserted in the text.
        preg_match_all('/(\w+\-\d+\.jpeg)/', $text, $matches);
        $paths = $matches[0];

        // Then retrieve the corresponding objects and use them to
        // generate the html code.
        $repository = $this->em->getRepository('KhatovarWebBundle:Photo');
        $photos = array();

        foreach ($paths as $path) {
            /**
             * @var Photo $photo
             */
            $photo = $repository->findOneByPath($path);
            if ($photo) {
                $photos[] = '<a href="/uploaded/photos/'
                    . $photo->getPath()
                    . '" data-lightbox="Photos Khatovar" title="Copyright &copy; '
                    . date('Y')
                    . ' association La Compagnie franche du Khatovar"><img class="'
                    . $photo->getClass()
                    . ' photo_rest" onmouseover="this.className=\''
                    . $photo->getClass()
                    . ' photo_over\'" onmouseout="this.className=\''
                    . $photo->getClass()
                    . ' photo_rest\'" src="/uploaded/photos/'
                    . $photo->getPath()
                    . '" alt="' . $photo->getAlt()
                    . '" /></a>';
            } else {
                $photos[] = 'Cette photo n\'existe pas';
            }
        }

        return str_replace($paths, $photos, $text);
    }
}
