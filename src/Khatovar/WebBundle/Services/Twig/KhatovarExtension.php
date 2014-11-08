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

namespace Khatovar\WebBundle\Services\Twig;

use Khatovar\WebBundle\Entity\Photo;

/**
 * Twig extension for KhatovarWebBundle.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\WebBundle\Services\Twig
 */
class KhatovarExtension extends \Twig_Extension
{
    /**
     * The minimum length between floating photos in a page.
     */
    const PARAGRAPH_LENGTH = 500;

    /**
     * Return the filters defined in this class.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter(
                'popup_picture',
                array($this, 'popupPicture'),
                array('is_safe' => array('html'))
            ),
            new \Twig_SimpleFilter(
                'link_picture',
                array($this, 'linkPicture'),
                array('is_safe' => array('html'))
            ),
            new \Twig_SimpleFilter(
                'link_picture',
                array($this, 'linkPicture'),
                array('is_safe' => array('html'))
            ),
            new \Twig_SimpleFilter(
                'thumbnail',
                array($this, 'thumbnail'),
                array('is_safe' => array('html'))
            ),
            new \Twig_SimpleFilter(
                'add_paragraph_and_photos',
                array($this, 'addParagraphAndPhotos'),
                array('is_safe' => array('html'))
            )
        );
    }

    /**
     * Return a picture as a link to display it using lightbox framework.
     *
     * @param string $path The path to the picture.
     * @param string $class The stylesheet class to use.
     * @param string $alt The alternative name of the picture.
     * @return string
     */
    public function popupPicture($path, $class = '', $alt = '')
    {
        $link = '<a href="' . $path
            . '" data-lightbox="Photos Khatovar" title="Copyright &copy; '
            . date('Y') . ' association La Compagnie franche du Khatovar"><img class="'
            . $class . ' photo_rest" onmouseover="this.className=\''
            . $class . ' photo_over\'" onmouseout="this.className=\'' . $class
            . ' photo_rest\'" src="' . $path . '" alt="' . $alt . '" /></a>';

        return $link;
    }

    /**
     * Return a link to display a picture with lightbox framework.
     *
     * @param string $path
     * @param string $text
     * @return string
     */
    public function linkPicture($path, $text = '')
    {
        $link = '<a href="' . $path . '" data-lightbox="' . $path
            . '" title="Copyright &copy; ' . date('Y')
            . ' association La Compagnie franche du Khatovar">' . $text . '</a>';

        return $link;
    }

    /**
     * .
     *
     * @param string $path
     * @param string $class
     * @param string $alt
     * @return string
     */
    public function thumbnail($path, $class, $alt = '')
    {
        $link = '<img src="' . $path . '" class="thumbnail ' . $class .' photo_rest" alt="' . $alt . '" />';

        return $link;
    }

    /**
     * Make an hyperlink from a thumbnail.
     *
     * @param string $destination The hyperlink
     * @param string $path The path of the picture for the thumbnail
     * @param string $class The class to apply to the thumbnail:
     *                      "portrait" or "landscape"
     * @param string $alt The alternative text for the thumbnail
     * @param string $text An optionnal text to display under the
     *                     thumbnail
     * @return string
     */
    public function thumbnailLink($path, $destination, $class, $alt = '', $text = '')
    {
        $link = '<div class="thumbnail_link ' . $class . ' photo_rest"
            onmouseover="this.className=\'thumbnail_link ' . $class . ' photo_over\';"
            onmouseout="this.className=\'thumbnail_link ' . $class . ' photo_rest\';"
            onclick="location.href=\'' . $destination . '\';">
            <img src="' . $path . '" class="trombinoscope" alt="' . $alt . '" /><br />'
            . $text . '</div>';

        return $link;
    }

    /**
     * Replace line breaks by paragraph and insert floatings between
     * paragraph.
     *
     * @param string $text The text to transform.
     * @param array $photos A list of photos to insert in the text.
     * @return string
     */
    public function addParagraphAndPhotos($text, array $photos)
    {
        // First we add the paragraph tags and a special break tag
        $text = '<p>' . $text . '</p>';

        // If text is too small, we return it directly without adding photos
        if (strlen($text) < $this::PARAGRAPH_LENGTH) {
            return str_replace("\n", "</p>\n<p>", $text);
        }

        $text = str_replace("\n", "</p>\n[break]<p>", $text);

        // We explode the text in paragraphs using the special tag
        $exploded = explode('[break]', $text);

        // We shuffle the photos and add the first one at the begining
        // of the text we will return.
        shuffle($photos);
        $result = $this->addFloat($photos[0], 'right');

        $photo = 1;
        $photosCount = count($photos);
        $paragraphs = count($exploded);

        // Then add the others
        for ($p = 0; $p < $paragraphs; $p++) {
            // If paragraph is longer than the define length, we add a float
            if (strlen($exploded[$p]) > $this::PARAGRAPH_LENGTH) {
                $result .= $exploded[$p];
                if ($photo < $photosCount) {
                    $result .= $this->addFloat(
                        $photos[$photo],
                        $photo % 2 ? 'left' : 'right'
                    );
                    $photo += 1;
                }
            // If it is not, we merge the current paragraph with the next one
            } elseif ($p + 1 < $paragraphs) {
                $exploded[$p+1] = $exploded[$p] . $exploded[$p+1];
                $exploded[$p] = '';
            }
        }

        return $result;
    }

    /**
     * Return the name of the extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'khatovar_extension';
    }

    /**
     * Return the html code to display a photo as a floating element in
     * a lightbox container.
     *
     * @param Photo $photo The photo to display.
     * @param string $side The side the photo float (left or right).
     * @return string
     */
    protected function addFloat(Photo $photo, $side)
    {
        $text = '<a href="' . $photo->getWebPath() . '" data-lightbox="Photos Khatovar"'
            . ' title="Copyright &copy; ' . date('Y') . ' association La Compagnie franche du Khatovar">'
            . '<img class="float float' . $side . ' photo_rest"'
            . ' onmouseover="this.className=\'float float' . $side . ' photo_over\'"'
            . ' onmouseout="this.className=\'float float' . $side . ' photo_rest\'"'
            . ' src="'. $photo->getWebPath() . '" alt="' . $photo->getAlt() . '" />'
            . '</a>';

        return $text . "\n";
    }
}
