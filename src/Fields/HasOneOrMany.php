<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Traits\AsSubResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\URL;

abstract class HasOneOrMany extends Relation
{
    use AsSubResource;

    /**
     * {@inheritdoc}
     */
    public function visible(RootRequest $request): bool
    {
        if ($this->asSubResource && $request instanceof CreateRequest) {
            return false;
        }

        return parent::visible($request);
    }

    /**
     * {@inheritdoc}
     */
    public function async(bool $value = true): static
    {
        parent::async($value);

        if ($this->asSubResource) {
            $this->component = 'SubResource';
        }

        return $this;
    }

    /**
     * Handle the resolving event on the field instance.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Cone\Root\Fields\Field  $field
     * @return void
     */
    protected function resolveField(RootRequest $request, Field $field): void
    {
        $field->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function registerRoutes(RootRequest $request, Router $router): void
    {
        parent::registerRoutes($request, $router);

        if ($this->asSubResource) {
            $router->prefix($this->getKey())->group(function (Router $router) use ($request): void {
                $this->resolveFields($request)->registerRoutes($request, $router);
                $this->resolveActions($request)->registerRoutes($request, $router);
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(RootRequest $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'related_name' => $this->getRelatedName(),
            'url' => URL::to(sprintf('%s/%s', $this->getUri(), $model->getKey())),
        ]);
    }
}
