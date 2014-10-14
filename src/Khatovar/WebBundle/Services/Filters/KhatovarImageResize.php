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

namespace Khatovar\WebBundle\Services\Filters;

/**
 * Class KhatovarImageResize
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\WebBundle\Services\Filters
 */
class KhatovarImageResize
{
    /**
     * Resize an jpeg image according to a given height, but only if
     * the original image is higher.
     *
     * @param string $image The path to the original image
     * @param int $new_height
     */
    public function imageResize($image, $new_height)
    {
        // We first find the dimensions of the photo and its ratio
        $original = imagecreatefromjpeg($image);
        list($width, $height) = getimagesize($image);
        $ratio = $width / $height;

        // Then define the new dimensions
        if ($height > $new_height) {
            $new_width = round($new_height * $ratio);

            // Then resize it with imagecopyresampled()
            $resized = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($resized, $original, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            // And finally replace the original by the new one on the server
            copy($image, $image . '.old');
            unlink($image);

            if (imagejpeg($resized, $image)) {
                unlink($image . '.old');
            } else {
                copy($image . '.old', $image);
            }

            imagedestroy($resized);
        }

        imagedestroy($original);
    }
}
