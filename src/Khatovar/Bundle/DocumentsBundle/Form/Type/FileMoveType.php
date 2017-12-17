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

use Khatovar\Bundle\DocumentsBundle\Entity\File;
use Khatovar\Bundle\DocumentsBundle\Entity\Folder;
use Khatovar\Bundle\DocumentsBundle\Entity\FolderRepository;
use Khatovar\Bundle\DocumentsBundle\Form\DataTransformer\FolderToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Form type to move files.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class FileMoveType extends AbstractType
{
    private const LEVEL_DISPLAY = '- ';

    /** @var FolderToNumberTransformer */
    private $folderToNumberTransformer;

    /** @var FolderRepository */
    private $repository;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * @param TranslatorInterface       $translator
     * @param FolderRepository          $repository
     * @param FolderToNumberTransformer $folderToNumberTransformer
     */
    public function __construct(
        TranslatorInterface $translator,
        FolderRepository $repository,
        FolderToNumberTransformer $folderToNumberTransformer
    ) {
        $this->translator = $translator;
        $this->repository = $repository;
        $this->folderToNumberTransformer = $folderToNumberTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('folder', ChoiceType::class, [
            'label' => $this->translator->trans('khatovar_documents.form.move.label'),
            'choices' => $this->getMoveList(),
        ]);

        $builder->get('folder')->addModelTransformer($this->folderToNumberTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => File::class]);
    }

    /**
     * Returns a hierarchic, formatted list of all folders.
     *
     * @return Folder[]
     */
    private function getMoveList(): array
    {
        $list = ['Racine' => 0];
        $parentlessFolders = $this->repository->findFoldersWithoutParentsOrderedByName();

        foreach ($parentlessFolders as $folder) {
            $listLabel = static::LEVEL_DISPLAY.$folder->getName();

            $list[$listLabel] = $folder->getId();
            $list = $this->addFolderChildrenToList($folder, $list, 2);
        }

        return $list;
    }

    /**
     * @param Folder $folder
     * @param array  $list
     * @param int    $level
     *
     * @return array
     */
    private function addFolderChildrenToList(Folder $folder, array $list, int $level): array
    {
        $folders = $this->repository->findChildrenOrderedByName($folder->getId());

        foreach ($folders as $folder) {
            $newLevel = $level + 1;
            $listLabel = str_repeat(static::LEVEL_DISPLAY, $level).$folder->getName();
            $list[$listLabel] = $folder->getId();

            $list = $this->addFolderChildrenToList($folder, $list, $newLevel);
        }

        return $list;
    }
}
