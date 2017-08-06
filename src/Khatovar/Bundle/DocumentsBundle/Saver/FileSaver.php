<?php

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
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

namespace Khatovar\Bundle\DocumentsBundle\Saver;

use Doctrine\Common\Util\ClassUtils;
use Khatovar\Bundle\DocumentsBundle\Entity\File;

/**
 * Saves a File entity.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class FileSaver extends BaseSaver
{
    /**
     * {@inheritdoc}
     *
     * The options are:
     *
     *  [
     *      'flush'  => bool,   # True to flush the object,false to only persist it.
     *      'folder' => int,    # The ID of the folder that will contain the file,
     *                          # only present when uploading a file.
     *  ]
     *
     * @return null|string the message to display after saving
     */
    public function save($file, array $options = [])
    {
        if (!$file instanceof $this->entityClass) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expects a "%s", "%s" provided.',
                    $this->entityClass,
                    ClassUtils::getClass($file)
                )
            );
        }

        $options = $this->optionsResolver->resolveSaveOptions($options);

        $message = $this->replaceExistingFile($file, $options);

        $this->entityManager->persist($file);

        if (true === $options['flush']) {
            $this->entityManager->flush();
        }

        return $message;
    }

    /**
     * If a file is uploaded at the same place than an existing one
     * with the same name, it will replace it.
     *
     * @param File  $file
     * @param array $options
     *
     * @return string
     */
    protected function replaceExistingFile(File $file, array $options)
    {
        if (isset($options['folder'])) {
            $fileExists = $this->entityManager->getRepository('KhatovarDocumentsBundle:File')->findOneBy(
                [
                    'name' => $file->getFilePath()->getClientOriginalName(),
                    'folder' => $options['folder'],
                ]
            );

            if (null !== $fileExists) {
                $file->setCreated($fileExists->getCreated());
                $this->entityManager->remove($fileExists);

                return 'khatovar_documents.notice.add.file.replace';
            }

            return 'khatovar_documents.notice.add.file.new';
        }

        return '';
    }
}
