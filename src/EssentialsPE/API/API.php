<?php

/**
 * EssentialsPE.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author iksaku
 * @link https://github.com/iksaku/EssentialsPE
 */

declare(strict_types=1);

namespace EssentialsPE\API;

use EssentialsPE\API\AFK\AFKManager;
use EssentialsPE\API\Commands\CommandManager;

class API extends Module
{
    protected function onEnable(): void
    {
        CommandManager::registerCoreCommands();

        // Enable all API Modules
        ($this->afkManager = new AFKManager())->enable();
    }

    protected function onDisable(): void
    {
        // Disable all API Modules
        $this->afkManager->disable();
        unset($this->afkManager);
    }

    /** @var ?AFKManager */
    private $afkManager;

    /**
     * Get an instance of the AFK Manager API.
     *
     * @return AFKManager
     */
    public function AFK(): AFKManager
    {
        return $this->afkManager;
    }

    // TODO: Homes API

    // TODO: Warps API

    // TODO: Translation API

    // TODO: Kits API

    // TODO: Conversation API (aka Messages API)
}
