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

namespace EssentialsPE\API\AFK;

use EssentialsPE\API\ISession;
use pocketmine\Player;

class AFKSession implements ISession
{
    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    /** @var Player */
    private $player;

    /**
     * Get owner of the Session.
     *
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /** @var bool */
    private $afk = false;

    /** @var int|null */
    private $afkTime = null;

    /**
     * Checks if the player is AFK.
     *
     * @return bool
     */
    public function isAFK(): bool
    {
        return $this->afk;
    }

    /**
     * Time at which the player was set as AFK.
     *
     * @return int|null
     */
    public function getAFKTime(): ?int
    {
        return $this->afkTime;
    }

    /**
     * Changes the AFK status of the player to a specific one.
     *
     * @param bool $state
     */
    public function setAFK(bool $state): void
    {
        $this->afk = $state;

        if ($this->isAFK()) {
            $this->afkTime = time();
        } else {
            $this->afkTime = null;
        }
    }

    /**
     * Toggles the AFK status of the player.
     */
    public function toggleAFK(): void
    {
        $this->setAFK(!$this->isAFK());
    }

    /** @var int|null */
    private $lastMoveTime = null;

    /**
     * Gets whether.
     *
     * @return int|null
     */
    public function getLastMoveTime(): ?int
    {
        return $this->lastMoveTime;
    }

    public function setLastMoveTime(?int $time = null): void
    {
        if (empty($time)) {
            $time = time();
        }

        $this->lastMoveTime = $time;
    }

    public function __destruct()
    {
    }
}
