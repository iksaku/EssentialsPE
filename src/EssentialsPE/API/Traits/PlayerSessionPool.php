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

namespace EssentialsPE\API\Traits;

use EssentialsPE\API\ISession;
use pocketmine\Player;

trait PlayerSessionPool
{
    /**
     * Modular Session object pool.
     *
     * @var ISession[]
     */
    protected $pool = [];

    /**
     * Looks for a player Session in the Session pool.
     *
     * @param Player $player
     * @return ISession|null
     */
    public function getSessionFor(Player $player): ?ISession
    {
        return $this->pool[$player->getUniqueId()] ?? null;
    }

    /**
     * Tells whether the specified player has a running Session in the pool.
     *
     * @param Player $player
     * @return bool
     */
    public function sessionExists(Player $player): bool
    {
        return $this->getSessionFor($player) !== null;
    }

    /**
     * Destroys Player Session.
     *
     * This may be due to Logout or Server Stopping.
     *
     * @param Player $player
     */
    public function destroySessionOf(Player $player): void
    {
        if (!$this->sessionExists($player)) {
            return;
        }

        unset($this->pool[$player->getUniqueId()]);
    }

    /**
     * Creates a Session for a player.
     *
     * @param Player $player
     * @param mixed|null $configuration
     */
    abstract public function createSessionFor(Player $player, $configuration = null): void;
}
