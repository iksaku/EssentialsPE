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

namespace EssentialsPE\API\Traits;

use EssentialsPE\Exceptions\API\Sessions\PlayerSessionNotFoundException;
use pocketmine\Player;

/**
 * @mixin PlayerSessionPool
 */
trait FunctionPipingToSession
{
    /**
     * Pipes function call to Session Objects.
     *
     * @param string $name
     * @param mixed[] $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        /** @var Player $player */
        $player = array_shift($arguments);

        $session = $this->getSessionFor($player);

        if (empty($session)) {
            throw new PlayerSessionNotFoundException();
        }

        return $session->$name(...$arguments);
    }
}
