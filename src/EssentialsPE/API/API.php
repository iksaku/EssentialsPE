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
use EssentialsPE\API\Traits\Singleton;

/**
 * @method static API getInstance()
 */
class API implements ISingleton
{
    use Singleton;

    public static function isEnabled(): bool
    {
        return true;
    }

    /**
     * @param bool[] $enabledModules
     */
    public static function enable(array $enabledModules): void
    {
    }

    public static function disable(): void
    {
        AFKManager::disable();
    }

    /**
     * Get an instance of the AFK Manager API.
     *
     * @return AFKManager
     */
    public static function AFK(): AFKManager
    {
        return AFKManager::getInstance();
    }

    // TODO: Homes API

    // TODO: Warps API

    // TODO: Translation API

    // TODO: Kits API

    // TODO: Conversation API (aka Messages API)
}
