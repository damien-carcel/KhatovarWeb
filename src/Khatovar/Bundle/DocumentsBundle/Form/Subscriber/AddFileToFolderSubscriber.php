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

namespace Khatovar\Bundle\DocumentsBundle\Form\Subscriber;

use Khatovar\Bundle\DocumentsBundle\Entity\Folder;
use Khatovar\Bundle\DocumentsBundle\Entity\FolderRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Add a "folder" field to the File form type.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class AddFileToFolderSubscriber implements EventSubscriberInterface
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
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'addParent',
        ];
    }

    /**
     * Adds a choice fields, listing all the folders in which one can
     * move a file or another folder.
     *
     * @param FormEvent $event
     */
    public function addParent(FormEvent $event): void
    {
        $form = $event->getForm();

        $form->add('folder', ChoiceType::class, [
            'label' => $this->translator->trans('khatovar_documents.form.move.label'),
            'choices' => $this->getMoveList(),
        ]);
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
