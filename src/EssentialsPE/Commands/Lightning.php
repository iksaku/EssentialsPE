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

namespace EssentialsPE\Commands;

use EssentialsPE\API\Commands\BaseCommand;
use EssentialsPE\EssentialsPE;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityIds;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Lightning extends BaseCommand
{
    private const BASE_PERMISSION = 'essentials.lightning';

    const INVOKE_PERMISSION = self::BASE_PERMISSION.'.use';

    const STRIKE_ANOTHER_PLAYER_PERMISSION = self::BASE_PERMISSION.'.player';

    public function __construct()
    {
        parent::__construct(
            'lightning',
            'Invoke the almighty power of Thor!',
            '[player] [damage]',
            '<player> [damage]',
            [
                'strike',
                'thor',
                'shock',
            ]
        );
    }

    public function getPermissions(): array
    {
        return [
            self::BASE_PERMISSION => [
                'description' => 'Give access to all Lightning BaseCommand functionalities',
                'default' => Permission::DEFAULT_OP,
                'children' => [
                    self::INVOKE_PERMISSION => [
                        'description' => 'Invoke the almighty power of Thor on yourself!',
                        'default' => Permission::DEFAULT_OP,
                    ],
                    self::STRIKE_ANOTHER_PLAYER_PERMISSION => [
                        'description' => 'Send a lightning to the position of another player',
                        'default' => Permission::DEFAULT_OP,
                    ],
                ],
            ],
        ];
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        parent::execute($sender, $commandLabel, $args);

        // Send usage message when Console doesn't specify target player.
        if (!$this->isPlayer($sender) && count($args) < 2) {
            $this->sendUsageMessage($sender);

            return false;
        }

        /** @var Player $targetPlayer */
        $targetPlayer = $sender;

        $damage = 0.0;

        if (isset($args[0])) {
            if (is_numeric($args[0]) && !isset($args[1])) {
                // Assign damage if first argument is numeric and there's no second argument.
                $damage = (float) $args[0];
            } else {
                // Bail out if CommandSender doesn't have permission to strike other players.
                if (!$this->hasPermission($sender, self::STRIKE_ANOTHER_PLAYER_PERMISSION)) {
                    return false;
                }

                // Since first argument isn't numeric, search for a player with the given name.
                $targetPlayer = EssentialsPE::getInstance()->getServer()->getPlayer($args[0]);

                if (is_null($targetPlayer)) {
                    $sender->sendMessage(TextFormat::RED."[Error] Unable to find Player '{$args[0]}'.");

                    return false;
                }
            }
        }

        if (isset($args[1])) {
            if (!is_numeric($args[1])) {
                // Fail if last argument isn't numeric.
                $sender->sendMessage(TextFormat::RED."[Error] Second argument represents 'damage' to deal with lightning, so, it must be a number.");

                return false;
            }

            $damage = (float) $args[1];
        }

        // Only damage target player if the target player is not the command sender.
        $this->strike($targetPlayer, $damage, $targetPlayer !== $sender);

        $sender->sendMessage(TextFormat::YELLOW.'Lightning Launched!');

        return true;
    }

    /**
     * @param Player $targetPlayer
     * @param float $damage
     * @param bool $shouldDamageTargetPlayer
     */
    private function strike(Player $targetPlayer, float $damage, bool $shouldDamageTargetPlayer): void
    {
        $lightningPacket = new AddActorPacket();
        $lightningPacket->entityRuntimeId = Entity::$entityCount++;
        $lightningPacket->type = AddActorPacket::LEGACY_ID_MAP_BC[EntityIds::LIGHTNING_BOLT];
        $lightningPacket->position = $targetPlayer->getPosition()->asVector3();
        $lightningPacket->motion = new Vector3();

        foreach ($targetPlayer->getLevel()->getPlayers() as $p) {
            $p->batchDataPacket($lightningPacket);
        }

        if ($damage <= 0) {
            // There's no point on executing a 0 damage event...
            return;
        }

        $axis = new AxisAlignedBB(
            $targetPlayer->getPosition()->getFloorX() - 5,
            $targetPlayer->getPosition()->getFloorY() - 5,
            $targetPlayer->getPosition()->getFloorZ() - 5,

            $targetPlayer->getPosition()->getFloorX() + 5,
            $targetPlayer->getPosition()->getFloorY() + 5,
            $targetPlayer->getPosition()->getFloorZ() + 5,
        );

        foreach ($targetPlayer->getLevel()->getNearbyEntities($axis, $targetPlayer) as $nearbyEntity) {
            $nearbyEntity->attack(
                new EntityDamageEvent($nearbyEntity, EntityDamageEvent::CAUSE_MAGIC, $damage)
            );
        }

        if ($shouldDamageTargetPlayer) {
            $targetPlayer->attack(
                new EntityDamageEvent($targetPlayer, EntityDamageEvent::CAUSE_MAGIC, $damage)
            );
        }
    }
}
