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

use EssentialsPE\API\AFK\Tasks\AFKAsyncTaskDispatcher;
use EssentialsPE\API\IModule;
use EssentialsPE\API\Traits\FunctionPipingToSession;
use EssentialsPE\API\Traits\Module;
use EssentialsPE\API\Traits\PlayerSessionPool;
use EssentialsPE\API\Traits\Singleton;
use EssentialsPE\EssentialsPE;
use pocketmine\event\HandlerList;
use pocketmine\Player;
use pocketmine\scheduler\TaskHandler;

/**
 * AFK Manager Typing.
 * @method static AFKManager    getInstance()
 * @method static AFKSession    getSessionFor(Player $player)
 * @method static void          destroyInstance()
 *
 * AFK Session Methods
 * @method bool     isAFK(Player $player)
 * @method void     setAFK(Player $player, bool $state)
 * @method void     toggleAFK(Player $player)
 * @method int|null getLastMoveTime(Player $player)
 * @method void     setLastMoveTime(Player $player)
 */
class AFKManager implements IModule
{
    use Singleton, Module, PlayerSessionPool, FunctionPipingToSession;

    /** @var AFKEventListener|null */
    private $eventListener = null;

    /** @var TaskHandler|null */
    private $afkTaskDispatcher = null;

    /**
     * {@inheritdoc}
     */
    public function createSessionFor(Player $player): void
    {
        $this->pool[$player->getUniqueId()] = new AFKSession($player);
    }

    /**
     * {@inheritdoc}
     */
    public static function shouldBeEnabled(): bool
    {
        return EssentialsPE::getInstance()
                ->getConfig()
                ->getNested('afk.enable', true) === true;
    }

    /**
     * {@inheritdoc}
     */
    protected static function onEnable(): void
    {
        $instance = self::$instance = new static();

        $plugin = EssentialsPE::getInstance();
        $server = $plugin->getServer();

        foreach ($server->getOnlinePlayers() as $player) {
            $instance->createSessionFor($player);
        }

        if (!isset($instance->eventListener)) {
            $instance->eventListener = new AFKEventListener();
        }

        $server->getPluginManager()->registerEvents($instance->eventListener, $plugin);

        $instance->afkTaskDispatcher = $plugin
            ->getScheduler()
            ->scheduleRepeatingTask(new AFKAsyncTaskDispatcher($instance->pool), 20 * 60); // Every minute

        // TODO: Register Commands
    }

    /**
     * {@inheritdoc}
     */
    protected static function onDisable(): void
    {
        $instance = self::getInstance();

        // TODO: Remove Commands

        if (isset($instance->afkTaskDispatcher)) {
            $instance->afkTaskDispatcher->cancel();
            unset($instance->afkTaskDispatcher);
        }

        if (isset($instance->eventListener)) {
            HandlerList::unregisterAll($instance->eventListener);
            unset($instance->eventListener);
        }

        $instance->pool = [];
    }
}
