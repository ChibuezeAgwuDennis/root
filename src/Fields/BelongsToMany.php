<?php

namespace Cone\Root\Fields;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo as BelongsToRelation;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as EloquentRelation;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class BelongsToMany extends Relation
{
    /**
     * The pivot fields resolver callback.
     */
    protected ?Closure $pivotFieldsResolver = null;

    /**
     * The default pivot values that should be saved.
     */
    protected array $pivotValues = [];

    /**
     * Create a new relation field instance.
     */
    public function __construct(string $label, string $modelAttribute = null, Closure|string $relation = null)
    {
        parent::__construct($label, $modelAttribute, $relation);

        $this->setAttribute('multiple', true);
    }

    /**
     * {@inheritdoc}
     */
    public function getRelation(Model $model): EloquentRelation
    {
        $relation = parent::getRelation($model);

        return $relation->withPivot($relation->newPivot()->getKeyName());
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return [
            BelongsTo::make($this->getRelatedName(), 'related', static function (Pivot $model): BelongsToRelation {
                return $model->belongsTo(
                    get_class($model->getRelation('related')),
                    $model->getRelatedKey(),
                    $model->getForeignKey(),
                    'related'
                );
            })
            ->withRelatableQuery(function (Request $request, Builder $query, Pivot $model): Builder {
                return $this->resolveRelatableQuery($request, $model->pivotParent);
            })
            ->display(function (Model $model): mixed {
                return $this->resolveDisplay($model);
            }),
        ];
    }

    /**
     * Handle the callback for the field resolution.
     */
    protected function resolveField(Request $request, Field $field): void
    {
        $field->setAttribute('form', $this->getAttribute('form'));
        $field->resolveErrorsUsing($this->errorsResolver);
        $field->setModelAttribute(
            sprintf('%s.*.%s', $this->getModelAttribute(), $field->getModelAttribute())
        );
    }

    /**
     * Set the pivot field resolver.
     */
    public function withPivotFields(Closure $callback): static
    {
        $this->withFields($callback);

        $this->pivotFieldsResolver = function (Request $request, Model $model, Model $related) use ($callback): Fields {
            $fields = new Fields();

            $fields->register(Arr::wrap(call_user_func_array($callback, [$request])));

            $fields->each(function (Field $field) use ($model, $related): void {
                $attribute = sprintf(
                    '%s.%s.%s',
                    $this->getModelAttribute(),
                    $related->getKey(),
                    $key = $field->getModelAttribute()
                );

                $field->setModelAttribute($attribute)
                    ->name($attribute)
                    ->id($attribute)
                    ->value(function () use ($model, $related, $key): mixed {
                        return $related->getRelation($this->getRelation($model)->getPivotAccessor())->getAttribute($key);
                    });
            });

            return $fields;
        };

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueForHydrate(Request $request): mixed
    {
        $value = (array) parent::getValueForHydrate($request);

        $value = Arr::isList($value) ? array_fill_keys($value, []) : $value;

        return array_map(function (array $pivot): array {
            return array_merge($this->pivotValues, $pivot);
        }, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, Model $model, mixed $value): void
    {
        $model->saved(function (Model $model) use ($request, $value): void {
            $this->resolveHydrate($request, $model, $value);

            $this->getRelation($model)->sync($value);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, Model $model, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function (Request $request, Model $model, mixed $value): void {
                $relation = $this->getRelation($model);

                $results = $this->resolveRelatableQuery($request, $model)
                    ->findMany(array_keys($value))
                    ->each(static function (Model $related) use ($relation, $value): void {
                        $related->setRelation(
                            $relation->getPivotAccessor(),
                            $relation->newPivot($value[$related->getKey()])
                        );
                    });

                $model->setRelation($relation->getRelationName(), $results);
            };
        }

        parent::resolveHydrate($request, $model, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function mapRelated(Request $request, Model $model, Model $related): array
    {
        $relation = $this->getRelation($model);

        $pivot = $related->getRelation($relation->getPivotAccessor());

        $pivot->setRelation('related', $related);

        return parent::mapRelated($request, $model, $pivot);
    }

    /**
     * {@inheritdoc}
     */
    public function toOption(Request $request, Model $model, Model $related): array
    {
        $relation = $this->getRelation($model);

        if (! $related->relationLoaded($relation->getPivotAccessor())) {
            $related->setRelation($relation->getPivotAccessor(), $relation->newPivot());
        }

        $option = parent::toOption($request, $model, $related);

        $option['attrs']['name'] = sprintf(
            '%s[%s][%s]',
            $this->getAttribute('name'),
            $related->getKey(),
            $this->getRelation($model)->getRelatedPivotKeyName()
        );

        $option['fields'] = is_null($this->pivotFieldsResolver)
            ? []
            : call_user_func_array($this->pivotFieldsResolver, [$request, $model, $related])->mapToInputs($request, $model);

        return $option;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'relatedName' => $this->getRelatedName(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function toValidate(Request $request, Model $model): array
    {
        return array_merge(
            parent::toValidate($request, $model),
            $this->resolveFields($request)->mapToValidate($request, $model)
        );
    }
}
