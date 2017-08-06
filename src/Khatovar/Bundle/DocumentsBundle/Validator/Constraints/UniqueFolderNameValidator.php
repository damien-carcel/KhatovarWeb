<?php

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2015 Damien Carcel <damien.carcel@gmail.com>
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

namespace Khatovar\Bundle\DocumentsBundle\Validator\Constraints;

use Khatovar\Bundle\DocumentsBundle\Entity\Folder;
use Khatovar\Bundle\DocumentsBundle\Entity\FolderRepository;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Prevent that in a given folder, a new folder has the same name that
 * an existing one.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UniqueFolderNameValidator extends ConstraintValidator
{
    /** @var FolderRepository */
    protected $repository;

    /** @var TranslatorInterface */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator, FolderRepository $repository)
    {
        $this->translator = $translator;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($folder, Constraint $constraint)
    {
        if ($folder instanceof Folder) {
            $neighbors = $this->getNeighbors($folder);

            foreach ($neighbors as $neighbor) {
                if ($neighbor->getName() === $folder->getName() && $neighbor->getId() !== $folder->getId()) {
                    $this->context->buildViolation($this->translator->trans($constraint->message))
                        ->setParameter('%name%', $folder->getName())
                        ->addViolation();
                }
            }
        }
    }

    /**
     * @param Folder $folder
     *
     * @return Folder[]
     */
    protected function getNeighbors(Folder $folder)
    {
        $parent = $folder->getParent();

        if (null !== $parent) {
            return $parent->getChildren()->toArray();
        }

        return $this->repository->findFoldersWithoutParentsOrderedByName();
    }
}
