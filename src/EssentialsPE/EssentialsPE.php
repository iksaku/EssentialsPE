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

namespace EssentialsPE;

use EssentialsPE\API\API;
use EssentialsPE\API\ISingleton;
use EssentialsPE\Commands\Antioch;
use EssentialsPE\Commands\Essentials;
use EssentialsPE\Commands\Lightning;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;

class EssentialsPE extends PluginBase implements ISingleton
{
    /** @var EssentialsPE|null */
    private static $instance = null;

    /**
     * Get access to EssentialsPE plugin.
     *
     * @return EssentialsPE|null
     */
    public static function getInstance(): ?self
    {
        return self::$instance;
    }

    public static function destroyInstance(): void
    {
        self::$instance = null;
    }

    /**
     * Get access to EssentialsPE API.
     *
     * @return API
     */
    public static function getAPI(): API
    {
        return API::getInstance();
    }

    /**
     * Handles Plugin Initial Processes.
     */
    public function onEnable(): void
    {
        self::$instance = $this;

        $this->getServer()->getCommandMap()->registerAll('EssentialsPE', [
            new Antioch(),
            new Essentials(),
            new Lightning(),
        ]);
    }

    public function onDisable()
    {
        self::destroyInstance();

        API::destroyInstance();
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
