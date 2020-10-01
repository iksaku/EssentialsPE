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

namespace EssentialsPE\Tests;

use EssentialsPE\API\API;
use EssentialsPE\EssentialsPE;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class PluginBehavesLikeASingleton extends MockeryTestCase
{
    private static function plugin(bool $enable = true): EssentialsPE
    {
        /** @var EssentialsPE $mock */
        $mock = Mockery::mock(EssentialsPE::class)->makePartial();

        if ($enable) {
            $apiMock = Mockery::mock('overload:'.API::class);
            $apiMock->shouldReceive('enable', 'disable')->withNoArgs()->andReturn();

            $mock->onEnable();
        }

        return $mock;
    }

    /** @test  */
    public function singleton_is_not_set_initially(): void
    {
        $this->assertNull(self::plugin(false)::getInstance());
    }

    /** @test */
    public function singleton_is_created_when_enabling_plugin(): void
    {
        $plugin = self::plugin();

        $this->assertSame($plugin, $plugin::getInstance());
    }

    /** @test */
    public function singleton_is_not_overridden_when_creating_a_new_plugin_instance(): void
    {
        $firstInstance = self::plugin();
        $secondInstance = self::plugin(false);

        $this->assertNotSame($secondInstance, $firstInstance::getInstance());

        $this->assertSame($firstInstance, $firstInstance::getInstance());
        $this->assertSame($firstInstance, $secondInstance::getInstance());
    }

    /** @test */
    public function singleton_is_overridden_when_enabling_second_instance(): void
    {
        $firstInstance = self::plugin();
        $secondInstance = self::plugin();

        $this->assertNotSame($firstInstance, $firstInstance::getInstance());

        $this->assertSame($secondInstance, $firstInstance::getInstance());
        $this->assertSame($secondInstance, $secondInstance::getInstance());
    }

    /** @test */
    public function singleton_is_removed_when_disabling_plugin(): void
    {
        $firstInstance = self::plugin();
        $secondInstance = self::plugin();

        $this->assertNotNull($firstInstance);
        $this->assertNotNull($secondInstance);

        $secondInstance->onDisable();

        $this->assertNull($firstInstance::getInstance());
        $this->assertNull($secondInstance::getInstance());
    }
}
