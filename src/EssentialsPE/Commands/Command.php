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
use EssentialsPE\Exceptions\PermissionHasNoNameException;
use EssentialsPE\Exceptions\Permissions\MissingPermissionDefaultAccess;
use EssentialsPE\Exceptions\Permissions\MissingPermissionDescription;
use pocketmine\command\Command as PocketMineCommand;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use ReflectionClass;
use ReflectionException;

abstract class Command extends PocketMineCommand
{
    /** @var string|null */
    private $consoleUsage;

    /**
     * BaseCommand constructor.
     *
     * Allows specifying console-specific usage message.
     *
     * @param string $name
     * @param string $description
     * @param string $usageMessage
     * @param string|null $consoleUsage
     * @param array $aliases
     */
    public function __construct(string $name, string $description, string $usageMessage, ?string $consoleUsage = null, array $aliases = [])
    {
        parent::__construct($name, $description, "/{$name} {$usageMessage}", $aliases);

        if (isset($consoleUsage)) {
            $this->consoleUsage = $consoleUsage;
        }

        $this->registerPermissions($this->getPermissions());
    }

    /**
     * Gets command permissions' tree for registration
     *
     * @return array<string, array>
     */
    public abstract function getPermissions(): array;

    /**
     * Registers all Command Permissions.
     *
     * @see DefaultPermissions::registerCorePermissions()
     *
     * @param array|null $permissions
     * @param Permission|null $parent
     */
    public final function registerPermissions(array $permissions = null, Permission $parent = null): void
    {
        if (empty($permissions)) {
            return;
        }

        foreach ($permissions as $name => $data) {
            if (!isset($data['description'])) {
                throw new MissingPermissionDescription();
            }

            if (!isset($data['default'])) {
                throw new MissingPermissionDefaultAccess();
            }

            $permission = new Permission($name, $data['description'], $data['default']);

            if (!isset($parent)) {
                $parent = EssentialsPE::plugin()->getRootPermission();
            }

            $parent->getChildren()[$permission->getName()] = true;

            PermissionManager::getInstance()->addPermission($permission);

            if (isset($data['children'])) {
                $this->registerPermissions($data['children'], $permission);
            }
        }
    }

    /**
     * Sends corresponding usage message to command sender.
     *
     * @param CommandSender $sender
     */
    public function sendUsageMessage(CommandSender $sender): void
    {
        if (isset($this->consoleUsage) && !($sender instanceof Player)) {
            $sender->sendMessage($this->consoleUsage);
        } else {
            $sender->sendMessage($this->getUsage());
        }
    }

    /**
     * Checks if sender has a specific permission.
     * If it doesn't, it sends a proper message to the sender.
     *
     * @param CommandSender $sender
     * @param string $permission
     * @return bool
     */
    public function hasPermission(CommandSender $sender, string $permission): bool
    {
        if ($sender->hasPermission($permission)) {
            return true;
        }

        $sender->sendMessage($sender->getServer()->getLanguage()->translateString(TextFormat::RED . "%commands.generic.permission"));

        return false;
    }

    /**
     * Checks whether sender is a Player or not.
     *
     * @param CommandSender $sender
     * @return bool
     */
    public function isPlayer(CommandSender $sender): bool
    {
        return $sender instanceof Player;
    }

    public abstract function execute(CommandSender $sender, string $commandLabel, array $args): bool;
}