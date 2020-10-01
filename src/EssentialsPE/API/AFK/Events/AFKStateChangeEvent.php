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

namespace EssentialsPE\API\AFK\Events;

use EssentialsPE\API\Events\Dispatchable;
use pocketmine\event\Cancellable;
use pocketmine\event\Event;
use pocketmine\Player;

class AFKStateChangeEvent extends Event implements Cancellable
{
    use Dispatchable;

    /** @var Player */
    protected $player;

    /** @var bool */
    protected $isCurrentlyAFK;

    /** @var bool */
    protected $willBeSetAFK;

    /** @var bool */
    protected $notifyPlayer;

    protected function __construct(Player $player, bool $isCurrentlyAFK, bool $willBeSetAFK, bool $notifyPlayer)
    {
        $this->player = $player;
        $this->isCurrentlyAFK = $isCurrentlyAFK;
        $this->willBeSetAFK = $willBeSetAFK;
        $this->notifyPlayer = $notifyPlayer;
    }

    /*
     * Get Event's player.
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /*
     * Check if the player is currently AFK.
     */
    public function isCurrentlyAFK(): bool
    {
        return $this->isCurrentlyAFK;
    }

    /*
     * Check the proposed next AFK state for the player.
     */
    public function willBeSetAFK(): bool
    {
        return $this->willBeSetAFK;
    }

    /*
     * Allows the event handler to modify the next AFK state.
     */
    public function shouldBeSetAFK(bool $state): void
    {
        $this->willBeSetAFK = $state;
    }

    /*
     * Check if the player will be notified of his new AFK status.
     */
    public function willPlayerBeNotified(): bool
    {
        return $this->notifyPlayer;
    }

    /*
     * Allow to specify if the player should (or not) be notified of his AFK status change.
     */
    public function shouldNotifyPlayer(bool $notifyPlayer): void
    {
        $this->notifyPlayer = $notifyPlayer;
    }
}
