<?php

namespace Cone\Root\Interfaces\Support\Collections;

interface Assets
{
    /**
     * Register a new script.
     */
    public function script(string $key, string $path, ?string $url = null): void;

    /**
     * Register a new style.
     */
    public function style(string $key, string $path, ?string $url = null): void;

    /**
     * Register a new icon.
     */
    public function icon(string $key, string $path, ?string $url = null): void;

    /**
     * Get the registered scripts.
     *
     * @return \Cone\Root\Support\Collections\Assets
     */
    public function scripts(): static;

    /**
     * Get the registered styles.
     *
     * @return \Cone\Root\Support\Collections\Assets
     */
    public function styles(): static;

    /**
     * Get the registered icons.
     *
     * @return \Cone\Root\Support\Collections\Assets
     */
    public function icons(): static;
}
