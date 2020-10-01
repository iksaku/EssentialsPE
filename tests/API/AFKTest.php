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

namespace EssentialsPE\Tests\API;

use EssentialsPE\API\AFK\AFKManager;
use EssentialsPE\API\AFK\AFKSession;
use EssentialsPE\API\AFK\Events\AFKStateChangeEvent;
use EssentialsPE\Tests\Concerns\HandlesEvents;
use EssentialsPE\Tests\Concerns\WorksWithPlayers;
use EssentialsPE\Tests\Concerns\WorksWithSessions;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use pocketmine\event\Listener;

class AFKTest extends MockeryTestCase
{
    use HandlesEvents,
        WorksWithPlayers,
        WorksWithSessions;

    /** @test */
    public function akf_manager_return_null_if_session_is_not_found(): void
    {
        $manager = new AFKManager();

        $this->assertNull($manager->getSessionFor($this->newPlayer()));
    }

    /** @test */
    public function afk_manager_can_create_session_for_player(): void
    {
        $manager = new AFKManager();

        $player = $this->newPlayer();
        $session = $manager->createSessionFor($player);

        $this->assertInstanceOf(AFKSession::class, $session);
        $this->assertSame($player, $session->getPlayer());
    }

    /** @test */
    public function afk_manager_can_persist_multiple_player_sessions(): void
    {
        $manager = new AFKManager();

        $player1 = $this->newPlayer();
        $session1 = $manager->createSessionFor($player1);

        $player2 = $this->newPlayer();
        $session2 = $manager->createSessionFor($player2);

        $this->assertSame($session1, $manager->getSessionFor($player1));
        $this->assertSame($session2, $manager->getSessionFor($player2));

        $this->assertNotSame($session1, $session2);
    }

    /** @test */
    public function afk_manager_can_pipe_session_functions(): void
    {
        $manager = new AFKManager();

        $player = $this->newPlayer();
        $this->overloadSession(AFKSession::class, [
            'isAFK' => true,
            'setAFK' => null,
            'switchAFKStatus' => null,
            'getAFKTime' => null,
            'getLastMoveTime' => null,
            'setLastMoveTime' => null,
        ]);

        $manager->createSessionFor($player);

        $manager->isAFK($player);
        $manager->setAFK($player, true);
        $manager->switchAFKStatus($player);
        $manager->getAFKTime($player);
        $manager->getLastMoveTime($player);
        $manager->setLastMoveTime($player);
    }

    /** @test */
    public function player_can_be_set_afk(): void
    {
        $manager = new AFKManager();

        /** @var AFKSession $session */
        $session = $manager->createSessionFor($this->newPlayer());

        $this->assertFalse($session->isAFK());

        $session->setAFK(true);

        $this->assertTrue($session->isAFK());

        $session->switchAFKStatus();

        $this->assertFalse($session->isAFK());
    }

    /** @test */
    public function player_can_be_notified_of_afk_status_change(): void
    {
        $manager = new AFKManager();

        $player = $this->newPlayer(['sendMessage' => null]);

        /** @var AFKSession $session */
        $session = $manager->createSessionFor($player);

        $session->setAFK(true, true);
    }

    /** @test */
    public function can_skip_player_notification_of_afk_status_change(): void
    {
        $manager = new AFKManager();

        $player = $this->newPlayer();
        $player->shouldNotReceive('sendMessage');

        /** @var AFKSession $session */
        $session = $manager->createSessionFor($player);

        $session->setAFK(true, false);
    }

    /** @test */
    public function it_can_skip_afk_status_change_if_event_is_cancelled(): void
    {
        $manager = new AFKManager();

        /** @var AFKSession $session */
        $session = $manager->createSessionFor($p = $this->newPlayer());

        $this->registerTestEventListener(new CancelAFKEvent());

        $this->assertFalse($session->isAFK());

        $session->setAFK(true);

        $this->assertFalse($session->isAFK());
    }

    /** @test */
    public function can_register_akf_set_time(): void
    {
        $manager = new AFKManager();

        /** @var AFKSession $session */
        $session = $manager->createSessionFor($this->newPlayer());

        $this->assertNull($session->getAFKTime());

        $session->setAFK(true);

        $this->assertIsInt($session->getAFKTime());
    }

    /** @test */
    public function can_record_last_player_move_time(): void
    {
        $manager = new AFKManager();

        /** @var AFKSession $session */
        $session = $manager->createSessionFor($this->newPlayer());

        $this->assertNull($session->getLastMoveTime());

        $session->setLastMoveTime($time = time());

        $this->assertSame($time, $session->getLastMoveTime());
    }
}

class CancelAFKEvent implements Listener
{
    /**
     * @param AFKStateChangeEvent $ev
     *
     * @priority MONITOR
     */
    public function onAFKStateChange(AFKStateChangeEvent $ev): void
    {
        $ev->setCancelled(true);
    }
}
