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

use EssentialsPE\EssentialsPE;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\utils\TextFormat;

class Essentials extends Command
{
    const INVOKE_PERMISSION = 'essentials.essentials';

    public function __construct()
    {
        parent::__construct(
            'essentials',
            'See which EssentialsPE version the server is running',
            '',
            null,
            [
                'ess',
                'essentialspe',
                'esspe'
            ]);
    }

    public function getPermissions(): array
    {
        return [
            self::INVOKE_PERMISSION => [
                'description' => 'See which EssentialsPE version the server is running',
                'default' => Permission::DEFAULT_OP
            ]
        ];
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        parent::execute($sender, $commandLabel, $args);

        $sender->sendMessage(
            TextFormat::YELLOW . "This server is using ".
            TextFormat::AQUA . "EssentialsPE".
            TextFormat::YELLOW . " version ".
            TextFormat::GREEN . EssentialsPE::plugin()->getDescription()->getVersion()
        );

        return true;
    }
}