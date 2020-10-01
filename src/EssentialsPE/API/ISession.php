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

use pocketmine\Player;

interface ISession
{
    /**
     * Get owner of the Session.
     *
     * @return Player
     */
    public function getPlayer(): Player;

    /**
     * Calls Session Destruction.
     *
     * This step may be critical for Persistent Data saving on some modules.
     */
    public function __destruct();
}
