<?php

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

use Khatovar\Bundle\DocumentsBundle\Entity\File;
use Khatovar\Bundle\DocumentsBundle\Entity\Folder;
use Khatovar\Bundle\DocumentsBundle\Entity\FolderRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Add a "parent" field to the Folder form type.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class AddParentSubscriber implements EventSubscriberInterface
{
    /** @var FolderRepository */
    protected $repository;

    /** @var TranslatorInterface */
    protected $translator;

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
    public static function getSubscribedEvents()
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
    public function addParent(FormEvent $event)
    {
        $item = $event->getData();
        $form = $event->getForm();
        $label = $this->translator->trans('khatovar_documents.form.move.folder.label');

        if ($item instanceof Folder) {
            $choices = $this->getMoveList($item);

            $form->add(
                'parent',
                EntityType::class,
                [
                    'class' => Folder::class,
                    'choices' => $choices,
                    'choice_label' => 'tempName',
                    'multiple' => false,
                    'label' => $label,
                ]
            );
        }

        if ($item instanceof File) {
            $choices = $this->getMoveList($item->getFolder(), $item);

            $form->add(
                'folder',
                EntityType::class,
                [
                    'class' => Folder::class,
                    'choices' => $choices,
                    'choice_label' => 'tempName',
                    'multiple' => false,
                    'label' => $label,
                ]
            );
        }
    }

    /**
     * Returns a hierarchic, formatted list of all folders.
     *
     * @param Folder $folder the folder to move, or the parent of the file to move
     * @param File   $file   the file to move, if any
     *
     * @return Folder[]
     */
    protected function getMoveList(Folder $folder, File $file = null)
    {
        $root = $this->repository->find(0);
        $root->setTempName($root->getName());

        $list = $this->createDirList([$root], '└', 0, $folder, $file);

        return $list;
    }

    /**
     * Creates a hierarchic, well formatted list of all folders present
     * in database, minus the parent of the element we want to move,
     * and if it is a folder the element itself and its children.
     *
     * @param Folder[] $list           the list of folder
     * @param string   $level          the current folder hierarchy
     * @param int      $folderId       the ID of the current handled folder
     * @param Folder   $originalFolder the folder to move, or the parent of the file to move
     * @param File     $file           the file to move, if any
     *
     * @return Folder[]
     */
    protected function createDirList($list, $level, $folderId, Folder $originalFolder, File $file = null)
    {
        if (null !== $file || (null === $file && $folderId !== $originalFolder->getId())) {
            $folders = $this->repository->findChildrenOrderedByName($folderId);

            foreach ($folders as $folder) {
                $newLevel = $level.'–';
                $folder->setTempName($level.'>'.$folder->getName());

                if (null !== $file || (null === $file && $folder !== $originalFolder)) {
                    $list[] = $folder;
                }

                $list = $this->createDirList($list, $newLevel, $folder->getId(), $originalFolder, $file);
            }
        }

        return $list;
    }
}
