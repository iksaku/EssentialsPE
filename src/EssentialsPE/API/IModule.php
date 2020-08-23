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

namespace EssentialsPE\API;

interface IModule extends ISingleton
{
    /**
     * Tells whether the Module is enabled or not.
     *
     * Useful for modularization.
     *
     * @return bool
     */
    public static function isEnabled(): bool;

    /**
     * Tells whether the Module should be initialized or not.
     *
     * This is based on EssentialsPE Configuration File.
     *
     * @return bool
     */
    public static function shouldBeEnabled(): bool;

    /**
     * Enables the Module.
     */
    public static function enable(): void;

    /**
     * Disables the Module.
     */
    public static function disable(): void;
}
