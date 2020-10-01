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

use EssentialsPE\EssentialsPE;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;

class AFKEventListener implements Listener
{
    /**
     * @param PlayerJoinEvent $event
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        EssentialsPE::API()->AFK()->createSessionFor($event->getPlayer());
    }

    /**
     * @param PlayerQuitEvent $event
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        EssentialsPE::API()->AFK()->destroySessionOf($event->getPlayer());
    }

    /**
     * @param PlayerMoveEvent $event
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function onPlayerMove(PlayerMoveEvent $event): void
    {
        EssentialsPE::API()->AFK()->setLastMoveTime($event->getPlayer());
    }
}
