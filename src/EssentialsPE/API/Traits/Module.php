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

use EssentialsPE\API\IModule;

/**
 * @mixin IModule
 */
trait Module
{
    /** @var bool */
    private static $enabled = false;

    /**
     * Tells if the Module is Enabled.
     *
     * @return bool
     */
    public static function isEnabled(): bool
    {
        return static::$enabled;
    }

    /**
     * {@inheritdoc}
     */
    public static function enable(): void
    {
        if (static::isEnabled() || !static::shouldBeEnabled()) {
            return;
        }

        static::onEnable();

        static::$enabled = true;
    }

    /**
     * Called when Module is Enabled.
     */
    abstract protected static function onEnable();

    /**
     * {@inheritdoc}
     */
    public static function disable(): void
    {
        if (!static::isEnabled()) {
            return;
        }

        static::$enabled = false;

        static::onDisable();

        static::destroyInstance();
    }

    /**
     * Called when Module is Disabled.
     */
    abstract protected static function onDisable(): void;

    /**
     * Called when Module is being cleared from Memory.
     */
    public function __destruct()
    {
        static::disable();
    }
}
