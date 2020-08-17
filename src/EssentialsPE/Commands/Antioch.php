<?php

/**
 * EssentialsPE
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

use pocketmine\block\Block;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\utils\Random;
use pocketmine\utils\TextFormat;

class Antioch extends Command
{
    const INVOKE_PERMISSION = 'essentials.antioch';

    protected $canBeExecutedByConsole = false;

    public function __construct()
    {
        parent::__construct(
            'antioch',
            'Holy hand grenade',
            '',
            null,
            [
                'grenade',
                'tnt'
            ]
        );
    }

    public function getPermissions(): array
    {
        return [
            self::INVOKE_PERMISSION => [
                'description' => "Place ignited TNT at the spot you're looking at",
                'default' => Permission::DEFAULT_OP
            ]
        ];
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        parent::execute($sender, $commandLabel, $args);

        /** @var Player $sender */

        $targetBlock = $sender->getTargetBlock(100, [
            Block::AIR => true,
            Block::FLOWING_WATER => true,
            Block::STILL_WATER => true,
            Block::FLOWING_LAVA => true,
            Block::STILL_LAVA => true
        ]);

        if (empty($targetBlock)) {
            $sender->sendMessage(TextFormat::YELLOW . "You can't throw your grenade that far away!");
            return false;
        }

        if (!$this->throwGrenade($targetBlock)) {
            $sender->sendMessage(TextFormat::YELLOW . "Oops, your grenade had an issue and didn't explode.");
            return false;
        }

        $sender->sendMessage(TextFormat::GREEN . 'Grenade thrown!');
        return true;
    }

    /**
     * @see \pocketmine\block\TNT::ignite()
     *
     * @param Block $target
     * @param int $fuse
     * @return bool
     */
    private function throwGrenade(Block $target, int $fuse = 80): bool
    {
        $mot = (new Random())->nextSignedFloat() * M_PI * 2;
        $nbt = Entity::createBaseNBT($target->add(0.5, 1, 0.5), new Vector3(-sin($mot) * 0.02, 0.2, -cos($mot) * 0.02));
        $nbt->setShort("Fuse", $fuse);

        $tnt = Entity::createEntity("PrimedTNT", $target->getLevelNonNull(), $nbt);

        if(empty($tnt)){
            return false;
        }

        $tnt->spawnToAll();

        return true;
    }
}