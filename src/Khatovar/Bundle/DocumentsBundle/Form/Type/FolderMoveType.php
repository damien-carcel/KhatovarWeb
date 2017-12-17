<?php

declare(strict_types=1);

/*
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

namespace Khatovar\Bundle\DocumentsBundle\Form\Type;

use Khatovar\Bundle\DocumentsBundle\Entity\Folder;
use Khatovar\Bundle\DocumentsBundle\Entity\FolderRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Form type to move folders.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class FolderMoveType extends AbstractType
{
    private const LEVEL_DISPLAY = '- ';

    /** @var FolderRepository */
    private $repository;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     * @param FolderRepository    $repository
     */
    public function __construct(TranslatorInterface $translator, FolderRepository $repository)
    {
        $this->translator = $translator;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $folder = $builder->getData();

        $builder->add('parent', ChoiceType::class, [
            'label' => $this->translator->trans('khatovar_documents.form.move.label'),
            'choices' => $this->getMoveList($folder),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Folder::class]);
    }

    /**
     * Returns a hierarchic, formatted list of all folders.
     *
     * @param Folder $folderToMove
     *
     * @return Folder[]
     */
    private function getMoveList(Folder $folderToMove): array
    {
        $list = ['Racine' => 0];
        $parentlessFolders = $this->repository->findFoldersWithoutParentsOrderedByName();

        foreach ($parentlessFolders as $folder) {
            if ($folder === $folderToMove) {
                continue;
            }

            $listLabel = static::LEVEL_DISPLAY.$folder->getName();

            $list[$listLabel] = $folder->getId();
            $list = $this->addFolderChildrenToList($folder, $folderToMove, $list, 2);
        }

        return $list;
    }

    /**
     * Creates a hierarchic, well formatted list of all folders present in
     * database, minus the folder we want to move and its children.
     *
     * @param Folder $folder
     * @param Folder $folderToMove
     * @param array  $list
     * @param int    $level
     *
     * @return Folder[]
     */
    private function addFolderChildrenToList(Folder $folder, Folder $folderToMove, array $list, int $level): array
    {
        $folders = $this->repository->findChildrenOrderedByName($folder->getId());

        foreach ($folders as $folder) {
            if ($folder === $folderToMove) {
                continue;
            }

            $newLevel = $level + 1;
            $listLabel = str_repeat(static::LEVEL_DISPLAY, $level).$folder->getName();
            $list[$listLabel] = $folder->getId();

            $list = $this->addFolderChildrenToList($folder, $folderToMove, $list, $newLevel);
        }

        return $list;
    }
}
