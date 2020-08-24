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

namespace EssentialsPE\API\Commands;

use EssentialsPE\Commands\Antioch;
use EssentialsPE\Commands\Essentials;
use EssentialsPE\Commands\Lightning;
use EssentialsPE\EssentialsPE;

class CommandManager
{
    public static function register(string $commandClass): void
    {
        $disabledCommands = EssentialsPE::getInstance()->getConfig()->get('disabled-commands', []);

        $command = new $commandClass();

        if (is_array($disabledCommands) && !empty($disabledCommands)) {
            $aliases = $command->getAliases();
            array_unshift($aliases, $command->getName());
            foreach ($aliases as $a) {
                if (in_array($a, $disabledCommands)) {
                    EssentialsPE::getInstance()
                        ->getLogger()
                        ->debug(
                            "[EssentialsPE] Command '{$command->getName()}'"
                            .($a === $command->getName() ? '' : " (Aliased as '{$a}')")
                            .' was disabled in config, skipping.'
                        );

                    return;
                }
            }
        }

        EssentialsPE::getInstance()
            ->getLogger()
            ->debug("[EssentialsPE] Registering command '{$command->getName()}'...");

        EssentialsPE::getInstance()
            ->getServer()
            ->getCommandMap()
            ->register(EssentialsPE::getInstance()->getName(), $command);
    }

    public static function registerCommands(array $commands): void
    {
        foreach ($commands as $command) {
            self::register($command);
        }
    }

    public static function registerCoreCommands(): void
    {
        self::registerCommands([
            Antioch::class,
            Essentials::class,
            Lightning::class,
        ]);
    }
}
