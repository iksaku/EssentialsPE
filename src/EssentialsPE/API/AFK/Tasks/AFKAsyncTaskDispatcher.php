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

namespace EssentialsPE\API\AFK\Tasks;

use EssentialsPE\API\AFK\AFKSession;
use EssentialsPE\API\ISession;
use EssentialsPE\EssentialsPE;
use pocketmine\scheduler\Task;

class AFKAsyncTaskDispatcher extends Task
{
    /** @var AFKSession[] */
    private $sessions;

    /** @var EssentialsPE */
    private $plugin;

    /** @var int */
    private $afkTogglePeriod;

    /** @var int */
    private $afkKickPeriod;

    /**
     * @param AFKSession[]|ISession[] $sessions
     */
    public function __construct(array $sessions)
    {
        $this->sessions = $sessions;

        $this->plugin = EssentialsPE::getInstance();

        $this->afkTogglePeriod = (int) $this->plugin->getConfig()->getNested('afk.auto-set', -1);
        $this->afkKickPeriod = (int) $this->plugin->getConfig()->getNested('afk.auto-kick', -1);
    }

    /**
     * {@inheritdoc}
     */
    public function onRun(int $currentTick): void
    {
        if ($this->afkTogglePeriod <= 0 && $this->afkKickPeriod <= 0) {
            $this->getHandler()->cancel();

            return;
        }

        $this->plugin
            ->getServer()
            ->getAsyncPool()
            ->submitTask(
                new AFKAsyncCheckerTask($this->afkTogglePeriod, $this->afkKickPeriod, $this->sessions)
            );
    }
}
