<?php
/**
 *
 * This file is part of Documents.
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
 * @link        https://github.com/damien-carcel/Documents
 * @license     http://www.gnu.org/licenses/gpl.html
 */

namespace Carcel\DocumentsBundle\Controller;

use Carcel\DocumentsBundle\Entity\File;
use Carcel\DocumentsBundle\Entity\Folder;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DocumentsController
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Carcel\DocumentsBundle\Controller
 */
class DocumentsController extends Controller
{
    /**
     * Return to the view the content of a folder, both files and
     * folders.
     * It also return the folder itself (allow to display its name and
     * use its ID to make a “previous link”, and a list of all previous
     * folders allowing to rapidely move up in the folder hierarchy.
     *
     * @param Folder $folder
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function indexAction(Folder $folder)
    {
        if (!$this->get('security.context')->isGranted('ROLE_VIEWER')) {
            return $this->render('CarcelUserBundle:Security:noRole.html.twig');
        }

        $folderRepo = $this->getDoctrine()->getManager()
            ->getRepository('CarcelDocumentsBundle:Folder');

        // TODO: Find another way to order files by names, without quering again the current folder
        $currentFolder = $folderRepo->getWithFiles($folder->getId());

        $previousFolders = array();
        $previousId = $currentFolder->getParent()->getId();
        while ($previousId >= 0) {
            $previousFolder = $folderRepo->find($previousId);
            $previousFolders[] = $previousFolder;
            $previousId = $previousFolder->getParent()->getId();
        }

        $folders = $folderRepo->findBy(
            array('parent' => $currentFolder->getId()),
            array('name' => 'ASC')
        );

        return $this->render(
            'CarcelDocumentsBundle:Documents:index.html.twig',
            array(
                'currentFolder' => $currentFolder,
                'folders' => $folders,
                'previousFolders' => array_reverse($previousFolders)
            )
        );
    }

    /**
     * Allow to directly download a file on the server.
     *
     * @param File $file
     * @return Response
     * @Secure(roles="ROLE_VIEWER")
     */
    public function shareAction(File $file)
    {
        // This prevents Firefox to truncate the name of the file if there is space in it
        $filename = str_replace(' ', '_', $file->getName());

        $response = new Response();
        $response->setContent(
            file_get_contents($file->getWebDir() . '/' . $file->getFileName())
        );
        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set(
            'Content-disposition',
            'filename=' . $filename
        );

        return $response;
    }

    /**
     * Add a new folder.
     *
     * @param Folder $parent The folder inside which we want to create a new one.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Secure(roles="ROLE_ADMIN")
     */
    public function addFolderAction(Folder $parent)
    {
        $folder = new Folder();
        $folder->setParent($parent);

        $form = $this->createFormBuilder($folder)
            ->add('name', 'text', array('label' => 'Nom : '))
            ->add('submit', 'submit', array('label' => 'Ajouter'))
            ->getForm();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($folder);
                $entityManager->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', 'Nouveau dossier ajouté.');

                return $this->redirect(
                    $this->generateUrl(
                        'carcel_documents_homepage',
                        array('folder' => $parent->getId())
                    )
                );
            }
        }

        return $this->render(
            'CarcelDocumentsBundle:Documents:addFolder.html.twig',
            array(
                'form' => $form->createView(),
                'previous' => $parent->getId()
            )
        );
    }

    /**
     * Add a new file.
     * If a file with the same name, in the same folder, already exists,
     * it will be deleted first.
     *
     * @param Folder $folder The folder inside which we want to upload a file.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Secure(roles="ROLE_UPLOADER")
     */
    public function addFileAction(Folder $folder)
    {
        $file = new File();
        $file->setFolder($folder);

        $form = $this->createFormBuilder($file)
            ->add('filePath', 'file', array('label' => 'Fichier : '))
            ->add('submit', 'submit', array('label' => 'Envoyer'))
            ->getForm();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();

                $fileExists = $entityManager
                    ->getRepository('CarcelDocumentsBundle:File')
                    ->findOneBy(
                        array(
                            'name' => $file->getFilePath()->getClientOriginalName(),
                            'folder' => $folder->getId()
                        )
                    );

                if (isset($fileExists) and !empty($fileExists)) {
                    $file->setCreated($fileExists->getCreated());
                    $entityManager->remove($fileExists);

                    $this->get('session')->getFlashBag()
                        ->add('notice', 'Le fichier a bien été remplacé.');
                } else {
                    $this->get('session')->getFlashBag()
                        ->add('notice', 'Le fichier a bien été ajouté.');
                }

                $entityManager->persist($file);
                $entityManager->flush();

                return $this->redirect(
                    $this->generateUrl(
                        'carcel_documents_homepage',
                        array('folder' => $folder->getId())
                    )
                );
            }
        }

        return $this->render(
            'CarcelDocumentsBundle:Documents:addFile.html.twig',
            array(
                'form' => $form->createView(),
                'previous' => $folder->getId()
            )
        );
    }

    /**
     * Move a folder into a new one.
     *
     * @param Folder $folder The folder to move.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Secure(roles="ROLE_ADMIN")
     */
    public function moveFolderAction(Folder $folder)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $folderList = $entityManager
            ->getRepository('CarcelDocumentsBundle:Folder')
            ->getMoveList($folder);

        $form = $this->createFormBuilder($folder)
            ->add(
                'parent',
                'entity',
                array(
                    'class' => 'CarcelDocumentsBundle:Folder',
                    'choices' => $folderList,
                    'property' => 'tempName',
                    'multiple' => false,
                    'label' => 'Choisissez un dossier : '
                )
            )
            ->add('submit', 'submit', array('label' => 'Déplacer'))
            ->getForm();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $entityManager->persist($folder);
                $entityManager->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', 'Le dossier a bien été déplacé.');

                return $this->redirect(
                    $this->generateUrl(
                        'carcel_documents_homepage',
                        array('folder' => $folder->getParent()->getId())
                    )
                );
            }
        }

        return $this->render(
            'CarcelDocumentsBundle:Documents:moveFolder.html.twig',
            array(
                'form' => $form->createView(),
                'previous' => $folder->getParent()->getId()
            )
        );
    }

    /**
     * Move a file into a new folder.
     *
     * @param File $file The file to move.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Secure(roles="ROLE_ADMIN")
     */
    public function moveFileAction(File $file)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $folderList = $entityManager
            ->getRepository('CarcelDocumentsBundle:Folder')
            ->getMoveList($file->getFolder(), $file);

        $form = $this->createFormBuilder($file)
            ->add(
                'folder',
                'entity',
                array(
                    'class' => 'CarcelDocumentsBundle:Folder',
                    'choices' => $folderList,
                    'property' => 'tempName',
                    'multiple' => false,
                    'label' => 'Choisissez un dossier : '
                )
            )
            ->add('submit', 'submit', array('label' => 'Déplacer'))
            ->getForm();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $entityManager->persist($file);
                $entityManager->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', 'Le fichier a bien été déplacé.');

                return $this->redirect(
                    $this->generateUrl(
                        'carcel_documents_homepage',
                        array('folder' => $file->getFolder()->getId())
                    )
                );
            }
        }

        return $this->render(
            'CarcelDocumentsBundle:Documents:moveFile.html.twig',
            array(
                'form' => $form->createView(),
                'previous' => $file->getFolder()->getId()
            )
        );
    }

    /**
     * Rename an existing folder.
     *
     * @param Folder $folder The Folder instance we want rename.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Secure(roles="ROLE_ADMIN")
     */
    public function renameFolderAction(Folder $folder)
    {
        $form = $this->createFormBuilder($folder)
            ->add('name', 'text', array('label' => 'Nouveau nom : '))
            ->add('submit', 'submit', array('label' => 'Renommer'))
            ->getForm();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($folder);
                $entityManager->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', 'Le dossier a bien été renommé.');

                return $this->redirect(
                    $this->generateUrl(
                        'carcel_documents_homepage',
                        array('folder' => $folder->getParent()->getId())
                    )
                );
            }
        }

        return $this->render(
            'CarcelDocumentsBundle:Documents:renameFolder.html.twig',
            array(
                'form' => $form->createView(),
                'previous' => $folder->getParent()->getId()
            )
        );
    }

    /**
     * Rename an existing file.
     *
     * @param File $file The File instance we want to rename.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Secure(roles="ROLE_ADMIN")
     */
    public function renameFileAction(File $file)
    {
        $form = $this->createFormBuilder($file)
            ->add('name', 'text', array('label' => 'Nouveau nom : '))
            ->add('submit', 'submit', array('label' => 'Renommer'))
            ->getForm();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($file);
                $entityManager->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', 'Le fichier a bien été renommé.');

                return $this->redirect(
                    $this->generateUrl(
                        'carcel_documents_homepage',
                        array('folder' => $file->getFolder()->getId())
                    )
                );
            }
        }

        return $this->render(
            'CarcelDocumentsBundle:Documents:renameFile.html.twig',
            array(
                'form' => $form->createView(),
                'previous' => $file->getFolder()->getId()
            )
        );
    }

    /**
     * Delete a folder and all its content.
     *
     * @param Folder $folder The folder to delete.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Secure(roles="ROLE_ADMIN")
     */
    public function removeFolderAction(Folder $folder)
    {
        $form = $this->createFormBuilder()->getForm();
        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($folder);
                $entityManager->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', 'Le dossier a bien été supprimé.');

                return $this->redirect(
                    $this->generateUrl(
                        'carcel_documents_homepage',
                        array('folder' => $folder->getParent()->getId())
                    )
                );
            }
        }

        return $this->render(
            'CarcelDocumentsBundle:Documents:removeFolder.html.twig',
            array('folder' => $folder, 'form' => $form->createView())
        );
    }

    /**
     * Delete a file on the server and its entry in database.
     *
     * @param File $file The file to delete.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Secure(roles="ROLE_ADMIN")
     */
    public function removeFileAction(File $file)
    {
        $form = $this->createFormBuilder()->getForm();
        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($file);
                $entityManager->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', 'Les fichier a bien été supprimé.');

                return $this->redirect(
                    $this->generateUrl(
                        'carcel_documents_homepage',
                        array('folder'=> $file->getFolder()->getId())
                    )
                );
            }
        }

        return $this->render(
            'CarcelDocumentsBundle:Documents:removeFile.html.twig',
            array('file' => $file, 'form' => $form->createView())
        );
    }
}
