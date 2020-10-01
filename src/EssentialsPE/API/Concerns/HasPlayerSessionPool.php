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

namespace EssentialsPE\API\Concerns;

use BadMethodCallException;
use EssentialsPE\API\ISession;
use EssentialsPE\Exceptions\API\Sessions\PlayerSessionNotFoundException;
use InvalidArgumentException;
use pocketmine\Player;

trait HasPlayerSessionPool
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
        return $this->pool[(string) $player->getUniqueId()] ?? null;
    }

    /**
     * Tells whether the specified player has a running Session in the pool.
     *
     * @param Player $player
     * @return bool
     */
    public function hasSessionFor(Player $player): bool
    {
        return $this->getSessionFor($player) !== null;
    }

    /**
     * Creates a Session for a player.
     *
     * @param Player $player
     * @return ISession
     */
    public function createSessionFor(Player $player): ISession
    {
        $sessionClass = $this->getSessionClass();

        $session = new $sessionClass($player);

        $this->pool[(string) $player->getUniqueId()] = $session;

        return $session;
    }

    /**
     * Specify the class to use when creating new player sessions.
     *
     * @retrun string
     */
    abstract protected function getSessionClass(): string;

    /**
     * Destroys Player Session.
     *
     * This may be due to Logout or Server Stopping.
     *
     * @param Player $player
     */
    public function destroySessionOf(Player $player): void
    {
        if (!$this->hasSessionFor($player)) {
            return;
        }

        unset($this->pool[$player->getUniqueId()]);
    }

    /**
     * Pipes function call to Session Objects.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (in_array($name, ['__destruct', 'getPlayer'])) {
            throw new BadMethodCallException("You're not allowed to call method {$name}() in Sessions");
        }

        $player = array_shift($arguments);

        if (!($player instanceof Player)) {
            throw new InvalidArgumentException();
        }

        $session = $this->getSessionFor($player);

        if (empty($session)) {
            throw new PlayerSessionNotFoundException();
        }

        return $session->$name(...$arguments);
    }
}
