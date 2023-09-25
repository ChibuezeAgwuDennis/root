<?php

namespace Cone\Root\Traits;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait ResolvesModelValue
{
    /**
     * The format resolver callback.
     */
    protected ?Closure $formatResolver = null;

    /**
     * The value resolver callback.
     */
    protected ?Closure $valueResolver = null;

    /**
     * Get the model.
     */
    abstract public function getModel(): Model;

    /**
     * Get the model attribute.
     */
    abstract public function getModelAttribute(): string;

    /**
     * Set the value resolver.
     */
    public function value(Closure $callback): static
    {
        $this->valueResolver = $callback;

        return $this;
    }

    /**
     * Resolve the value.
     */
    public function resolveValue(Request $request): mixed
    {
        $value = $this->getValue();

        if (is_null($this->valueResolver)) {
            return $value;
        }

        return call_user_func_array($this->valueResolver, [$request, $this->getModel(), $value]);
    }

    /**
     * Get the default value from the model.
     */
    public function getValue(): mixed
    {
        return $this->getModel()->getAttribute($this->getModelAttribute());
    }

    /**
     * Set the format resolver.
     */
    public function format(Closure $callback): static
    {
        $this->formatResolver = $callback;

        return $this;
    }

    /**
     * Format the value.
     */
    public function resolveFormat(Request $request): mixed
    {
        $value = $this->resolveValue($request);

        if (is_null($this->formatResolver)) {
            return $value;
        }

        return call_user_func_array($this->formatResolver, [$request, $this->getModel(), $value]);
    }
}