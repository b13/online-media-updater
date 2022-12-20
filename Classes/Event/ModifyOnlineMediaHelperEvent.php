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

use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\AbstractOnlineMediaHelper;

final class ModifyOnlineMediaHelperEvent
{
    public function __construct(protected ?AbstractOnlineMediaHelper $onlineMediaHelper)
    {
    }

    /**
     * @return AbstractOnlineMediaHelper|null
     */
    public function getOnlineMediaHelper(): ?AbstractOnlineMediaHelper
    {
        return $this->onlineMediaHelper;
    }

    /**
     * @param AbstractOnlineMediaHelper|null $onlineMediaHelper
     */
    public function setOnlineMediaHelper(?AbstractOnlineMediaHelper $onlineMediaHelper): void
    {
        $this->onlineMediaHelper = $onlineMediaHelper;
    }
}
