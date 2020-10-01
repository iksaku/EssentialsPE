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

use EssentialsPE\API\AFK\Events\AFKStateChangeEvent;
use EssentialsPE\API\ISession;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class AFKSession implements ISession
{
    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    /** @var Player */
    private $player;

    /**
     * {@inheritdoc}
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
     * Changes the AFK status of the player to a specific one.
     *
     * @param bool $state
     * @param bool $notify
     */
    public function setAFK(bool $state, bool $notify = true): void
    {
        if ($this->isAFK() === $state) {
            // AFK state will not change, no action needed.
            return;
        }

        $ev = AFKStateChangeEvent::dispatch($this->getPlayer(), $this->isAFK(), $state, $notify);

        if ($ev->isCancelled() || $this->isAFK() === $ev->willBeSetAFK()) {
            // AFK state will no longer change, no action needed.
            return;
        }

        $this->afk = $ev->willBeSetAFK();

        $this->afkTime = $this->isAFK()
            ? time()
            : null;

        if ($ev->willPlayerBeNotified()) {
            $this->getPlayer()->sendMessage(
                TextFormat::YELLOW."You're ".($this->isAFK() ? 'now' : 'no longer').' AFK.'
            );
        }
    }

    /**
     * Toggles the AFK status of the player.
     *
     * @param bool $notify
     */
    public function switchAFKStatus(bool $notify = true): void
    {
        $this->setAFK(!$this->isAFK(), $notify);
    }

    /**
     * Time at which the player was set as AFK.
     * Used for kicking AFK players from the server.
     *
     * @return int|null
     */
    public function getAFKTime(): ?int
    {
        return $this->afkTime;
    }

    /** @var int|null */
    private $lastMoveTime = null;

    /**
     * Gets the timestamp in which the player last moved.
     * Used to set idle players in AFK mode.
     *
     * @return int|null
     */
    public function getLastMoveTime(): ?int
    {
        return $this->lastMoveTime;
    }

    /**
     * Saves the most recent player movement as a timestamp.
     *
     * @param int|null $time
     */
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
