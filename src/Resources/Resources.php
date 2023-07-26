<?php

namespace Cone\Root\Resources;

use Cone\Root\Exceptions\ResourceResolutionException;
use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;

class Resources
{
    use ForwardsCalls;

    /**
     * The resources collections.
     */
    protected Collection $resources;

    /**
     * Create a new controller instance.
     */
    public function __construct(array $resources = [])
    {
        $this->resources = new Collection($resources);
    }

    /**
     * Register the given resource into the collection.
     */
    public function register(array|Resource $resources): void
    {
        foreach (Arr::wrap($resources) as $resource) {
            $this->resources->put($resource->getKey(), $resource);
        }
    }

    /**
     * Resolve the resource registered with the given key.
     */
    public function resolve(string $key): Resource
    {
        if (! $this->resources->has($key)) {
            throw new ResourceResolutionException();
        }

        return $this->resources->get($key);
    }

    /**
     * Filter the authorized resources.
     */
    public function authorized(Request $request): static
    {
        return $this->resources->filter->authorized($request)->values();
    }

    /**
     * Handle the dynamic method call.
     */
    public function __call($method, $parameters): mixed
    {
        return $this->forwardCallTo($this->resources, $method, $parameters);
    }
}
