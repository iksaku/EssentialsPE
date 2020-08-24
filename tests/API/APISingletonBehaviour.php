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

namespace EssentialsPE\Tests\API;

use EssentialsPE\API\API;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class APISingletonBehaviour extends MockeryTestCase
{
    public function test_singleton_is_always_available(): void
    {
        $this->assertTrue(API::isEnabled());

        $this->assertNotNull(API::getInstance());
    }
}
