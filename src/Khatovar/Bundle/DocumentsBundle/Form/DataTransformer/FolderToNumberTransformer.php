<?php

declare(strict_types=1);

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2017 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Khatovar\Bundle\DocumentsBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectRepository;
use Khatovar\Bundle\DocumentsBundle\Entity\Folder;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class FolderToNumberTransformer implements DataTransformerInterface
{
    /** @var ObjectRepository */
    private $folderRepository;

    /**
     * @param ObjectRepository $folderRepository
     */
    public function __construct(ObjectRepository $folderRepository)
    {
        $this->folderRepository = $folderRepository;
    }

    /**
     * @param Folder $folder
     *
     * @return int|null
     */
    public function transform($folder): ?int
    {
        if (!$folder instanceof Folder) {
            return null;
        }

        return $folder->getId();
    }

    /**
     * @param int $id
     *
     * @return Folder|null
     */
    public function reverseTransform($id): ?Folder
    {
        $folder = $this->folderRepository->find($id);

        return $folder;
    }
}
