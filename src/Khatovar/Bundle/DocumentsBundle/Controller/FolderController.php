<?php

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2015 Damien Carcel (https://github.com/damien-carcel)

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

namespace Khatovar\Bundle\DocumentsBundle\Controller;

use Khatovar\Bundle\DocumentsBundle\Entity\Folder;
use Khatovar\Bundle\DocumentsBundle\Form\Type\FolderMoveType;
use Khatovar\Bundle\DocumentsBundle\Form\Type\FolderType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class FolderController extends Controller
{
    /**
     * Returns to the view the content of the root, both files and
     * folders with no parents.
     *
     * @return Response
     */
    public function indexAction()
    {
        if (null === $this->getUser()) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        if (!$this->isGranted('ROLE_VIEWER')) {
            return $this->render('KhatovarUserBundle:Security:no_role.html.twig');
        }

        $rootChildren = $this->get('khatovar_documents.repositories.folder')->findFoldersWithoutParentsOrderedByName();

        $folderDeleteForms = $this
            ->get('khatovar_documents.creator.form')
            ->createDeleteForms($rootChildren, 'khatovar_documents_remove_folder');

        $files = $this->get('khatovar_documents.repositories.file')->findFilesWithoutParentsOrderedByName();

        $fileDeleteForms = $this
            ->get('khatovar_documents.creator.form')
            ->createDeleteForms($files, 'khatovar_documents_remove_file');

        return $this->render(
            'KhatovarDocumentsBundle:Folder:folder.html.twig',
            [
                'children' => $rootChildren,
                'folder_delete_forms' => $folderDeleteForms,
                'files' => $files,
                'file_delete_forms' => $fileDeleteForms,
            ]
        );
    }

    /**
     * Returns to the view the content of a folder, both files and
     * folders.
     * It also returns the folder itself (allow to display its name and
     * use its ID to make a “previous link”), and a list of all previous
     * folders allowing to rapidly move up in the folder hierarchy.
     *
     * @param string $id
     *
     * @return Response
     */
    public function folderAction($id)
    {
        if (null === $this->getUser()) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        if (!$this->isGranted('ROLE_VIEWER')) {
            return $this->render('KhatovarUserBundle:Security:no_role.html.twig');
        }

        $currentFolder = $this->findFolderByIdWithOrderedFilesOr404($id);
        $children = $this->findChildrenOrderedByName($id);
        $previousFolders = $this->getFolderParentsByFolderIdOr404($id);

        $folderDeleteForms = $this
            ->get('khatovar_documents.creator.form')
            ->createDeleteForms($children, 'khatovar_documents_remove_folder');

        $fileDeleteForms = $this
            ->get('khatovar_documents.creator.form')
            ->createDeleteForms($currentFolder->getFiles()->toArray(), 'khatovar_documents_remove_file');

        return $this->render(
            'KhatovarDocumentsBundle:Folder:folder.html.twig',
            [
                'current_folder' => $currentFolder,
                'children' => $children,
                'previous_folders' => $previousFolders,
                'folder_delete_forms' => $folderDeleteForms,
                'file_delete_forms' => $fileDeleteForms,
            ]
        );
    }

    /**
     * Adds a new folder.
     *
     * @param Request $request
     * @param string  $parentId
     *
     * @return Response
     *
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addFolderAction(Request $request, $parentId)
    {
        $folder = $this->get('khatovar_documents.factory.folder')->createFolder($parentId);

        $form = $this->get('khatovar_documents.creator.form')->createCreateForm(
            $folder,
            FolderType::class,
            'khatovar_documents_add_folder'
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('khatovar_documents.saver.folder')->save($folder);

            $this->addFlash(
                'notice',
                $this->get('translator')->trans('khatovar_documents.notice.add.folder')
            );

            if ('0' !== $parentId) {
                return $this->redirect($this->generateUrl(
                    'khatovar_documents_folder',
                    ['id' => $parentId]
                ));
            }

            return $this->redirect($this->generateUrl('khatovar_documents_homepage'));
        }

        return $this->render(
            'KhatovarDocumentsBundle:Folder:add.html.twig',
            [
                'form' => $form->createView(),
                'previousId' => $parentId,
            ]
        );
    }

    /**
     * Moves a folder into a new one.
     *
     * @param Request $request
     * @param string  $id
     *
     * @return Response
     *
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function moveFolderAction(Request $request, $id)
    {
        $folder = $this->findFolderByIdOr404($id);

        $form = $this->get('khatovar_documents.creator.form')->createMoveForm(
            $folder,
            FolderMoveType::class,
            'khatovar_documents_move_folder'
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('khatovar_documents.saver.folder')->save($folder);

            $this->addFlash(
                'notice',
                $this->get('translator')->trans('khatovar_documents.notice.move.folder')
            );

            if (null !== $folder->getParent()) {
                return $this->redirect($this->generateUrl(
                    'khatovar_documents_folder',
                    ['id' => $folder->getParent()->getId()]
                ));
            }

            return $this->redirect($this->generateUrl('khatovar_documents_homepage'));
        }

        return $this->render(
            'KhatovarDocumentsBundle:Folder:move.html.twig',
            [
                'form' => $form->createView(),
                'previousId' => $folder->getParent() ? $folder->getParent()->getId() : null,
            ]
        );
    }

    /**
     * Renames an existing folder.
     *
     * @param Request $request
     * @param string  $id
     *
     * @return Response
     *
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function renameFolderAction(Request $request, $id)
    {
        $folder = $this->findFolderByIdOr404($id);

        $form = $this->get('khatovar_documents.creator.form')->createEditForm(
            $folder,
            FolderType::class,
            'khatovar_documents_rename_folder'
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('khatovar_documents.saver.folder')->save($folder);

            $this->addFlash(
                'notice',
                $this->get('translator')->trans('khatovar_documents.notice.rename.folder')
            );

            if (null !== $folder->getParent()) {
                return $this->redirect($this->generateUrl(
                    'khatovar_documents_folder',
                    ['id' => $folder->getParent()->getId()]
                ));
            }

            return $this->redirect($this->generateUrl('khatovar_documents_homepage'));
        }

        return $this->render(
            'KhatovarDocumentsBundle:Folder:rename.html.twig',
            [
                'form' => $form->createView(),
                'previousId' => $folder->getParent() ? $folder->getParent()->getId() : null,
            ]
        );
    }

    /**
     * Deletes a folder and all its content.
     *
     * @param Request $request
     * @param string  $id
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @return Response
     *
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function removeFolderAction(Request $request, $id)
    {
        $folder = $this->findFolderByIdOr404($id);

        $form = $this
            ->get('khatovar_documents.creator.form')
            ->createDeleteForm($id, 'khatovar_documents_remove_folder');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->get('doctrine.orm.entity_manager');
            $entityManager->remove($folder);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                $this->get('translator')->trans('khatovar_documents.notice.remove.folder')
            );
        }

        if (null !== $folder->getParent()) {
            return $this->redirect($this->generateUrl(
                'khatovar_documents_folder',
                ['id' => $folder->getParent()->getId()]
            ));
        }

        return $this->redirect($this->generateUrl('khatovar_documents_homepage'));
    }

    /**
     * Finds and returns a Folder entity from its ID.
     *
     * @param string $id
     *
     * @throws NotFoundHttpException
     *
     * @return Folder
     */
    protected function findFolderByIdOr404($id)
    {
        $folder = $this->get('khatovar_documents.repositories.folder')->find($id);

        if (null === $folder) {
            throw new NotFoundHttpException(sprintf('The folder with the ID %d does not exists', $id));
        }

        return $folder;
    }

    /**
     * Finds and returns a folder's parents from its ID.
     *
     * @param string $id
     *
     * @throws NotFoundHttpException
     *
     * @return Folder
     */
    protected function findFolderByIdWithOrderedFilesOr404($id)
    {
        $folder = $this->get('khatovar_documents.repositories.folder')->findOneWithOrderedFiles($id);

        if (null === $folder) {
            throw new NotFoundHttpException(sprintf('The folder with the ID %d does not exists', $id));
        }

        return $folder;
    }

    /**
     * Finds and returns a folder's children from its ID.
     *
     * @param string $id
     *
     * @return Folder[]
     */
    protected function findChildrenOrderedByName($id)
    {
        return $this->get('khatovar_documents.repositories.folder')->findChildrenOrderedByName($id);
    }

    /**
     * Finds and returns a folder's parents from its ID.
     *
     * @param string $id
     *
     * @throws NotFoundHttpException
     *
     * @return Folder[]
     */
    protected function getFolderParentsByFolderIdOr404($id)
    {
        $folders = $this->get('khatovar_documents.repositories.folder')->getParents($id);

        if (null === $folders) {
            throw new NotFoundHttpException(sprintf('The folder with the ID %d does not exists', $id));
        }

        return $folders;
    }
}
