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

namespace Khatovar\Bundle\DocumentsBundle\Controller;

use Khatovar\Bundle\DocumentsBundle\Entity\File;
use Khatovar\Bundle\DocumentsBundle\Form\Type\FileMoveType;
use Khatovar\Bundle\DocumentsBundle\Form\Type\FileType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class FileController extends Controller
{
    /**
     * Allows to directly download a file on the server.
     * Spaces in names are replaced by underscores to prevent errors in download.
     *
     * @param string $id
     *
     * @return Response
     *
     * @Security("has_role('ROLE_VIEWER')")
     */
    public function shareAction($id)
    {
        $file = $this->findFileByIdOr404($id);

        $filename = str_replace(' ', '_', $file->getName());
        $response = new Response();

        $response->setContent(file_get_contents($file->getWebDir().'/'.$file->getFileName()));
        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-disposition', 'filename='.$filename);

        return $response;
    }

    /**
     * Adds a new file.
     * If a file with the same name, in the same folder, already exists,
     * it will be deleted first.
     *
     * @param Request $request
     * @param int     $parentId
     *
     * @return Response
     *
     * @Security("has_role('ROLE_UPLOADER')")
     */
    public function addFileAction(Request $request, $parentId)
    {
        $file = $this->get('khatovar_documents.factory.file')->createFile($parentId);
        $form = $this->get('khatovar_documents.creator.form')->createCreateForm(
            $file,
            FileType::class,
            'khatovar_documents_add_file'
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $message = $this->get('khatovar_documents.saver.file')->save($file, ['folder' => $parentId]);

            $this->addFlash('notice', $message);

            if ('0' !== $parentId) {
                return $this->redirect($this->generateUrl(
                    'khatovar_documents_folder',
                    ['id' => $parentId]
                ));
            }

            return $this->redirect($this->generateUrl('khatovar_documents_homepage'));
        }

        return $this->render(
            'KhatovarDocumentsBundle:File:add.html.twig',
            [
                'form' => $form->createView(),
                'previousId' => $parentId,
            ]
        );
    }

    /**
     * Moves a file into a new folder.
     *
     * @param Request $request
     * @param string  $id
     *
     * @return Response
     *
     * @Security("has_role('ROLE_UPLOADER')")
     */
    public function moveFileAction(Request $request, $id)
    {
        $file = $this->findFileByIdOr404($id);

        $form = $this->get('khatovar_documents.creator.form')->createMoveForm(
            $file,
            FileMoveType::class,
            'khatovar_documents_move_file'
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('khatovar_documents.saver.file')->save($file);

            $this->addFlash(
                'notice',
                $this->get('translator')->trans('khatovar_documents.notice.move.file')
            );

            if (null !== $file->getFolder()) {
                return $this->redirect($this->generateUrl(
                    'khatovar_documents_folder',
                    ['id' => $file->getFolder()->getId()]
                ));
            }

            return $this->redirect($this->generateUrl('khatovar_documents_homepage'));
        }

        return $this->render(
            'KhatovarDocumentsBundle:File:move.html.twig',
            [
                'form' => $form->createView(),
                'previousId' => $file->getFolder()->getId(),
            ]
        );
    }

    /**
     * Renames an existing file.
     *
     * @param Request $request
     * @param string  $id
     *
     * @return Response
     *
     * @Security("has_role('ROLE_UPLOADER')")
     */
    public function renameFileAction(Request $request, $id)
    {
        $file = $this->findFileByIdOr404($id);

        $form = $this->get('khatovar_documents.creator.form')->createEditForm(
            $file,
            FileType::class,
            'khatovar_documents_rename_file'
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('khatovar_documents.saver.file')->save($file);

            $this->addFlash(
                'notice',
                $this->get('translator')->trans('khatovar_documents.notice.rename.file')
            );

            if (null !== $file->getFolder()) {
                return $this->redirect($this->generateUrl(
                    'khatovar_documents_folder',
                    ['id' => $file->getFolder()->getId()]
                ));
            }

            return $this->redirect($this->generateUrl('khatovar_documents_homepage'));
        }

        return $this->render(
            'KhatovarDocumentsBundle:File:rename.html.twig',
            [
                'form' => $form->createView(),
                'previousId' => $file->getFolder()->getId(),
            ]
        );
    }

    /**
     * Deletes a file on the server and its entry in database.
     *
     * @param Request $request
     * @param string  $id
     *
     * @return Response
     *
     * @Security("has_role('ROLE_UPLOADER')")
     */
    public function removeFileAction(Request $request, $id)
    {
        $file = $this->findFileByIdOr404($id);

        $form = $this->get('khatovar_documents.creator.form')->createDeleteForm($id, 'khatovar_documents_remove_file');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('doctrine.orm.entity_manager')->remove($file);
            $this->get('doctrine.orm.entity_manager')->flush();

            $this->addFlash(
                'notice',
                $this->get('translator')->trans('khatovar_documents.notice.remove.file')
            );
        }

        if (null !== $file->getFolder()) {
            return $this->redirect($this->generateUrl(
                'khatovar_documents_folder',
                ['id' => $file->getFolder()->getId()]
            ));
        }

        return $this->redirect($this->generateUrl('khatovar_documents_homepage'));
    }

    /**
     * Finds and returns a File entity from its ID.
     *
     * @param string $id
     *
     * @throws NotFoundHttpException
     *
     * @return File
     */
    protected function findFileByIdOr404($id)
    {
        $file = $this->get('khatovar_documents.repositories.file')->find($id);

        if (null === $file) {
            throw new NotFoundHttpException(sprintf('The file with the ID %d does not exists', $id));
        }

        return $file;
    }
}
