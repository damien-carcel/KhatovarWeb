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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class FileSubscriber implements EventSubscriberInterface
{
    /** @var TranslatorInterface */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'addFileFields',
        ];
    }

    /**
     * Adds fields to File form.
     *
     * @param FormEvent $event
     */
    public function addFileFields(FormEvent $event)
    {
        $file = $event->getData();
        $form = $event->getForm();

        if ($file instanceof File) {
            if (null === $file->getId()) {
                $this->addFilePathField($form);
            } else {
                $this->addNameField($form);
            }
        }
    }

    /**
     * Adds a file upload field.
     *
     * @param FormInterface $form
     */
    protected function addFilePathField(FormInterface $form)
    {
        $form->add(
            'filePath',
            FileType::class,
            ['label' => $this->translator->trans('khatovar_documents.form.add.file.label')]
        );
    }

    /**
     * Adds a name field.
     *
     * @param FormInterface $form
     */
    protected function addNameField(FormInterface $form)
    {
        $form->add(
            'name',
            TextType::class,
            ['label' => $this->translator->trans('khatovar_documents.form.rename.label')]
        );
    }
}
