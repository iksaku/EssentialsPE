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

namespace EssentialsPE\Tests\Concerns;

use EssentialsPE\EssentialsPE;
use Mockery;
use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginDescription;
use pocketmine\plugin\PluginManager;

trait HandlesEvents
{
    public function registerTestEventListener(Listener $listener): void
    {
        /** @var EssentialsPE|Mockery\MockInterface $plugin */
        $plugin = Mockery::mock('alias:'.EssentialsPE::class, Plugin::class)
            ->allows([
                'isEnabled' => true,
                'getDescription' => new PluginDescription([
                    'name' => 'EssentialsPE Tests',
                    'version' => 'PHPUnit',
                    'main' => 'EssentialsPE\EssentialsPE',
                ]),
            ]);

        /** @var PluginManager $pluginManager */
        $pluginManager = Mockery::mock(PluginManager::class)
            ->makePartial();

        $pluginManager->registerEvents($listener, $plugin);
    }
}
