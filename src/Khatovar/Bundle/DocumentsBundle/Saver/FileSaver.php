<?php

declare(strict_types=1);

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
     */
    public function save($file, array $options = []): void
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

        $this->replaceExistingFile($file, $options);

        $this->doctrine->getManager()->persist($file);

        if (true === $options['flush']) {
            $this->doctrine->getManager()->flush();
        }
    }

    /**
     * If a file is uploaded at the same place than an existing one
     * with the same name, it will replace it.
     *
     * @param File  $file
     * @param array $options
     */
    protected function replaceExistingFile(File $file, array $options): void
    {
        if (isset($options['folder'])) {
            $fileExists = $this->doctrine->getRepository('KhatovarDocumentsBundle:File')->findOneBy(
                [
                    'name' => $file->getFilePath()->getClientOriginalName(),
                    'folder' => $options['folder'],
                ]
            );

            if (null !== $fileExists) {
                $file->setCreated($fileExists->getCreated());
                $this->doctrine->getManager()->remove($fileExists);
            }
        }
    }
}
