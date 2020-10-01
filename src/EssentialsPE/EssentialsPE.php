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
use EssentialsPE\API\Singleton\Singleton;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;

class EssentialsPE extends PluginBase
{
    use Singleton;

    /** @var ?API */
    private $api;

    /*
     * Get access to EssentialsPE plugin.
     */
    public static function getInstance(): ?self
    {
        return self::$instance;
    }

    /*
     * Get access to EssentialsPE API.
     */
    public static function API(): ?API
    {
        return self::getInstance()->api;
    }

    /*
     * Handles Plugin Initial Processes.
     */
    public function onEnable(): void
    {
        self::$instance = $this;

        $this->api = new API();
        $this->api->enable();
    }

    /*
     * Handles Plugin End Processes.
     */
    public function onDisable()
    {
        self::destroyInstance();

        $this->api->disable();
        unset($this->api);
    }

    /** @var Permission */
    private $rootPermission;

    /*
     * Root EssentialsPE permission, the ruler of 'em all.
     */
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
