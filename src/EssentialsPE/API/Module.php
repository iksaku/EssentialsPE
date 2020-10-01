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

abstract class Module
{
    /** @var bool */
    protected $enabled = false;

    /**
     * Tells whether the Module is enabled or not.
     *
     * Useful for modularization.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Tells whether the Module should be initialized or not.
     *
     * This is based on EssentialsPE Configuration File.
     *
     * @return bool
     */
    public function shouldBeEnabled(): bool
    {
        return true;
    }

    /**
     * Enables the module.
     */
    public function enable(): void
    {
        if ($this->isEnabled() || !$this->shouldBeEnabled()) {
            return;
        }

        $this->onEnable();

        $this->enabled = true;
    }

    /**
     * Called during module enable process.
     */
    abstract protected function onEnable(): void;

    /**
     * Disables the module.
     */
    public function disable(): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $this->enabled = false;

        $this->onDisable();
    }

    /**
     * Called during module disable process.
     */
    abstract protected function onDisable(): void;

    /**
     * Ensure modules are disabled when removed from memory.
     */
    public function __destruct()
    {
        $this->disable();
    }
}
