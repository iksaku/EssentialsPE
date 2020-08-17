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

namespace EssentialsPE;

use EssentialsPE\Commands\Essentials;
use EssentialsPE\Commands\Lightning;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class EssentialsPE extends PluginBase
{
    private static $instance;

    /**
     * Get access to EssentialsPE plugin
     *
     * @return EssentialsPE
     */
    public static function plugin(): EssentialsPE
    {
        return self::$instance;
    }

    /**
     * Get access to PocketMine's Server API
     *
     * @return Server
     */
    public static function server(): Server
    {
        return self::plugin()->getServer();
    }

    /**
     * Handles Plugin Initial Processes
     */
    public function onEnable()
    {
        self::$instance = $this;

        $this->getServer()->getCommandMap()->registerAll('EssentialsPE', [
            new Essentials(),
            new Lightning()
        ]);
    }

    /** @var Permission */
    private $rootPermission;

    public function getRootPermission(): Permission
    {
        if (!isset($this->rootPermission)) {
            $this->rootPermission = new Permission(
                'essentials',
                'Gives access to all EssentialsPE features',
                Permission::DEFAULT_FALSE
            );

            PermissionManager::getInstance()->addPermission($this->rootPermission);
        }

        return $this->rootPermission;
    }
}