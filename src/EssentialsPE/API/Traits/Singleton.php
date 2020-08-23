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

use EssentialsPE\API\ISingleton;

/**
 * @mixin ISingleton
 */
trait Singleton
{
    /** @var ISingleton|null */
    private static $instance = null;

    final protected function __construct()
    {
    }

    /**
     * Singleton classes must exist only once.
     *
     * This pattern will help us implement multi-functional
     * API objects that isolate themselves, while forcing
     * only one instance on creation.
     *
     * @return ISingleton|null
     */
    public static function getInstance(): ?ISingleton
    {
        if (static::isEnabled() && !isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Destroys Singleton instance.
     */
    public static function destroyInstance(): void
    {
        static::$instance = null;
    }
}
