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

use EssentialsPE\API\Concerns\HasPlayerSessionPool;
use EssentialsPE\API\Module;
use EssentialsPE\EssentialsPE;
use pocketmine\event\HandlerList;
use pocketmine\Player;
use pocketmine\scheduler\TaskHandler;

/**
 * @method bool     isAFK(Player $player)
 * @method void     setAFK(Player $player, bool $state, bool $notify = true)
 * @method void     switchAFKStatus(Player $player, bool $notify = true)
 * @method int|null getAFKTime(Player $player)
 * @method int|null getLastMoveTime(Player $player)
 * @method void     setLastMoveTime(Player $player, ?int $time = null)
 */
class AFKManager extends Module
{
    use HasPlayerSessionPool;

    /** @var AFKEventListener|null */
    private $eventListener = null;

    /** @var TaskHandler|null */
    private $afkTaskDispatcher = null;

    /**
     * {@inheritdoc}
     */
    public function getSessionClass(): string
    {
        return AFKSession::class;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldBeEnabled(): bool
    {
        return EssentialsPE::getInstance()
                ->getConfig()
                ->getNested('afk.enable', true) === true;
    }

    /**
     * {@inheritdoc}
     */
    protected function onEnable(): void
    {
        $plugin = EssentialsPE::getInstance();
        $server = $plugin->getServer();

        foreach ($server->getOnlinePlayers() as $player) {
            $this->createSessionFor($player);
        }

        if (!isset($this->eventListener)) {
            $this->eventListener = new AFKEventListener();
        }

        $server->getPluginManager()->registerEvents($this->eventListener, $plugin);

//        TODO: AFK Monitor
//        $instance->afkTaskDispatcher = $plugin
//            ->getScheduler()
//            ->scheduleRepeatingTask(new AFKAsyncTaskDispatcher($instance->pool), 20 * 60); // Every minute

//        TODO: Register Commands
    }

    /**
     * {@inheritdoc}
     */
    protected function onDisable(): void
    {
        // TODO: Remove Commands

        if (isset($this->afkTaskDispatcher)) {
            $this->afkTaskDispatcher->cancel();
            unset($this->afkTaskDispatcher);
        }

        if (isset($this->eventListener)) {
            HandlerList::unregisterAll($this->eventListener);
            unset($this->eventListener);
        }

        $this->pool = [];
    }
}
