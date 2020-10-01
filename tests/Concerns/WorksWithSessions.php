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

use EssentialsPE\API\ISession;
use Mockery;

trait WorksWithSessions
{
    /**
     * @param string $class
     * @param array $shouldReceive
     * @param int $times
     * @return ISession|Mockery\MockInterface
     */
    public function overloadSession(string $class, array $shouldReceive, int $times = 1)
    {
        return Mockery::mock('overload:'.$class, ISession::class)
            ->shouldReceive($shouldReceive)
            ->times($times)
            ->getMock();
    }
}
