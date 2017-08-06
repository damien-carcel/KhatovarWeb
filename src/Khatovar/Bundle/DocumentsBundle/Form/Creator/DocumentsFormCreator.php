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

namespace Khatovar\Bundle\DocumentsBundle\Form\Creator;

use Khatovar\Bundle\DocumentsBundle\Entity\File;
use Khatovar\Bundle\DocumentsBundle\Entity\Folder;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Creates forms for Folder and File entities.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class DocumentsFormCreator implements DocumentsFormCreatorInterface
{
    /** @var FormFactoryInterface */
    protected $formFactory;

    /** @var RouterInterface */
    protected $router;

    /** @var TranslatorInterface */
    protected $translator;

    /**
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface      $router
     * @param TranslatorInterface  $translator
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function createCreateForm($item, $type, $url)
    {
        if ($item instanceof Folder) {
            $parentId = null !== $item->getParent() ? $item->getParent()->getId() : 0;
        } elseif ($item instanceof File) {
            $parentId = null !== $item->getFolder() ? $item->getFolder()->getId() : 0;
        } else {
            throw new InvalidArgumentException();
        }

        $form = $this->formFactory->create(
            $type,
            $item,
            [
                'action' => $this->router->generate($url, ['parentId' => $parentId]),
                'method' => 'POST',
            ]
        );

        $form->add(
            'submit',
            SubmitType::class,
            ['label' => $this->translator->trans('khatovar_documents.button.add')]
        );

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function createEditForm($item, $type, $url)
    {
        return $this->createBaseEditForm($item, $type, $url, 'khatovar_documents.button.rename');
    }

    /**
     * {@inheritdoc}
     */
    public function createMoveForm($item, $type, $url)
    {
        return $this->createBaseEditForm($item, $type, $url, 'khatovar_documents.button.move');
    }

    /**
     * {@inheritdoc}
     */
    public function createDeleteForm($id, $url)
    {
        $formBuilder = $this->formFactory->createBuilder();

        $formBuilder->setAction($this->router->generate($url, ['id' => $id]));
        $formBuilder->setMethod('DELETE');
        $formBuilder->add(
            'submit',
            SubmitType::class,
            [
                'label' => $this->translator->trans('khatovar_documents.button.delete'),
                'attr' => [
                    'onclick' => sprintf(
                        'return confirm("%s")',
                        $this->translator->trans('khatovar_documents.notice.delete.confirmation')
                    ),
                    'class' => 'btn btn-sm btn-default',
                ],
            ]
        );

        return $formBuilder->getForm();
    }

    /**
     * {@inheritdoc}
     */
    public function createDeleteForms(array $items, $url)
    {
        $deleteForms = [];

        foreach ($items as $item) {
            $deleteForms[$item->getId()] = $this->createDeleteForm($item->getId(), $url)->createView();
        }

        return $deleteForms;
    }

    /**
     * @param object $item
     * @param string $type
     * @param string $url
     * @param string $label
     *
     * @return FormInterface
     */
    protected function createBaseEditForm($item, $type, $url, $label)
    {
        $form = $this->formFactory->create(
            $type,
            $item,
            [
                'action' => $this->router->generate($url, ['id' => $item->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add(
            'submit',
            SubmitType::class,
            ['label' => $this->translator->trans($label)]
        );

        return $form;
    }
}
