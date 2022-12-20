<?php

declare(strict_types=1);

/**
 * This file is part of TYPO3 CMS-based extension "online-media-updater" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace B13\OnlineMediaUpdater\Event;

use TYPO3\CMS\Core\Resource\File;

final class ModifyMetaDataEvent
{
    public function __construct(
        protected ?File $fileObject,
        protected array $metaData
    )
    {
    }

    /**
     * @return File|null
     */
    public function getFileObject(): ?File
    {
        return $this->fileObject;
    }

    /**
     * @param File|null $fileObject
     */
    public function setFileObject(?File $fileObject): void
    {
        $this->fileObject = $fileObject;
    }

    /**
     * @return array
     */
    public function getMetaData(): array
    {
        return $this->metaData;
    }

    /**
     * @param array $metaData
     */
    public function setMetaData(array $metaData): void
    {
        $this->metaData = $metaData;
    }
}
