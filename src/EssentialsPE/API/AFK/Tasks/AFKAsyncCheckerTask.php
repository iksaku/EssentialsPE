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
use EssentialsPE\EssentialsPE;
use pocketmine\Player;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class AFKAsyncCheckerTask extends AsyncTask
{
    /**
     * Period (in seconds) in which a player must be set as AFK.
     *
     * Disabled if set to a negative value or zero.
     *
     * @var int
     */
    private $afkTogglePeriod;

    /**
     * Period (in seconds) in which an AFK player should be kicked from the server.
     *
     * Disabled if set to a negative value or zero.
     *
     * @var int
     */
    private $afkKickPeriod;

    /**
     * Current Session pool.
     *
     * @var AFKSession[]
     */
    private $sessions;

    /**
     * @param int $afkTogglePeriod
     * @param int $afkKickPeriod
     * @param AFKSession[] $sessions
     */
    public function __construct(int $afkTogglePeriod, int $afkKickPeriod, array $sessions)
    {
        $this->afkTogglePeriod = $afkTogglePeriod * 60;
        $this->afkKickPeriod = $afkKickPeriod * 60;

        $this->sessions = serialize($sessions);
    }

    /**
     * {@inheritdoc}
     */
    public function onRun(): void
    {
        $result = [];

        $now = time();

        foreach (unserialize($this->sessions) as $session) {
            /* @var AFKSession $session */

            $result[] = [
                'setAFK' => !$session->isAFK()
                    && $this->afkTogglePeriod > 0
                    && !empty($session->getLastMoveTime())
                    && ($now - $session->getLastMoveTime()) >= $this->afkTogglePeriod,
                'kick' => $session->isAFK()
                    && $this->afkKickPeriod > 0
                    && !empty($session->getAFKTime())
                    && ($now - $session->getAFKTime()) >= $this->afkKickPeriod,
                'player' => $session->getPlayer(),
            ];
        }

        $this->setResult($result);
    }

    public function onCompletion(Server $server): void
    {
        foreach ($this->getResult() as $data) {
            /** @var Player $player */
            $player = $data['player'];

            if ($data['kick']) {
                $player->kick("You've been kicked for being idle for more than {$this->afkKickPeriod} seconds");
            }

            if ($data['setAFK']) {
                EssentialsPE::API()::AFK()->setAFK($player, true);
            }
        }
    }
}
