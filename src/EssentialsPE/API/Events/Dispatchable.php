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

namespace EssentialsPE\API\Events;

use pocketmine\event\Event;

/**
 * @mixin Event
 */
trait Dispatchable
{
    /**
     * Little utility function that lets us get rid of boilerplate
     * event calls, when needed.
     *
     * @return static
     */
    public static function dispatch()
    {
        $ev = new static(...func_get_args());
        $ev->call();

        return $ev;
    }
}
