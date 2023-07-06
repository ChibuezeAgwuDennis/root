<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Resources\ModelValueHandler;
use Cone\Root\Traits\ResolvesVisibility;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

abstract class Field extends ModelValueHandler implements Arrayable
{
    use ResolvesVisibility;

    /**
     * The hydrate resolver callback.
     */
    protected ?Closure $hydrateResolver = null;

    /**
     * The validation rules.
     */
    protected array $rules = [
        '*' => [],
        'create' => [],
        'update' => [],
    ];

    /**
     * The blade component.
     */
    protected string $component = 'root::fields.text';

    /**
     * The help text for the field.
     */
    protected ?string $help = null;

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, string $name = null)
    {
        parent::__construct($label, $name);

        $this->id($this->name);
    }

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return $this->getKey();
    }

    /**
     * Get the blade component.
     */
    public function getComponent(): string
    {
        return $this->component;
    }

    /**
     * Set the label attribute.
     */
    public function label(string|Closure $value): static
    {
        return $this->setAttribute('label', $value);
    }

    /**
     * Set the name attribute.
     */
    public function name(string|Closure $value): static
    {
        return $this->setAttribute('name', $value);
    }

    /**
     * Set the id attribute.
     */
    public function id(string|Closure $value): static
    {
        return $this->setAttribute('id', $value);
    }

    /**
     * Set the readonly attribute.
     */
    public function readonly(bool|Closure $value = true): static
    {
        return $this->setAttribute('readonly', $value);
    }

    /**
     * Set the disabled attribute.
     */
    public function disabled(bool|Closure $value = true): static
    {
        return $this->setAttribute('disabled', $value);
    }

    /**
     * Set the required attribute.
     */
    public function required(bool|Closure $value = true): static
    {
        return $this->setAttribute('required', $value);
    }

    /**
     * Set the type attribute.
     */
    public function type(string|Closure $value): static
    {
        return $this->setAttribute('type', $value);
    }

    /**
     * Set the placeholder attribute.
     */
    public function placeholder(string|Closure $value): static
    {
        return $this->setAttribute('placeholder', $value);
    }

    /**
     * Set the help attribute.
     */
    public function help(?string $value = null): static
    {
        $this->help = $value;

        return $this;
    }

    /**
     * Persist the request value on the model.
     */
    public function persist(Request $request, Model $model, mixed $value): void
    {
        $model->saving(function (Model $model) use ($request, $value): void {
            $this->resolveHydrate($request, $model, $value);
        });
    }

    /**
     * Get the value for hydrating the model.
     */
    public function getValueForHydrate(Request $request, Model $model): mixed
    {
        return $request->input([$this->getKey()]);
    }

    /**
     * Set the hydrate resolver.
     */
    public function hydrate(Closure $callback): static
    {
        $this->hydrateResolver = $callback;

        return $this;
    }

    /**
     * Hydrate the model.
     */
    public function resolveHydrate(Request $request, Model $model, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function () use ($model, $value): void {
                $model->setAttribute($this->getKey(), $value);
            };
        }

        call_user_func_array($this->hydrateResolver, [$request, $model, $value]);
    }

    /**
     * Set the validation rules.
     */
    public function rules(array|Closure $rules, string $context = '*'): static
    {
        $this->rules[$context] = $rules;

        return $this;
    }

    /**
     * Set the create validation rules.
     */
    public function createRules(array|Closure $rules): static
    {
        return $this->rules($rules, 'create');
    }

    /**
     * Set the update validation rules.
     */
    public function updateRules(array|Closure $rules): static
    {
        return $this->rules($rules, 'update');
    }

    /**
     * Resolve the attributes.
     */
    public function resolveAttributes(Request $request, Model $model): array
    {
        return array_reduce(
            array_keys($this->attributes),
            function (array $attributes, string $key) use ($request, $model): mixed {
                return array_merge($attributes, [$key => $this->resolveAttribute($request, $model, $key)]);
            },
            []
        );
    }

    /**
     * Resolve the given attribute.
     */
    public function resolveAttribute(Request $request, Model $model, string $key): mixed
    {
        $value = $this->getAttribute($key);

        return $value instanceof Closure
                ? call_user_func_array($value, [$request, $model])
                : $value;
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        return $this->getAttributes();
    }

    /**
     * Get the display representation of the field.
     */
    public function toDisplay(Request $request, Model $model): array
    {
        return array_merge($this->resolveAttributes($request, $model), [
            'formattedValue' => $this->resolveFormat($request, $model),
            'value' => $this->resolveValue($request, $model),
        ]);
    }

    /**
     * Get the input representation of the field.
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge($this->resolveAttributes($request, $model), [
            'component' => $this->getComponent(),
            'formattedValue' => $this->resolveFormat($request, $model),
            'help' => $this->help,
            'value' => $this->resolveValue($request, $model),
        ]);
    }

    /**
     * Get the validation representation of the field.
     */
    public function toValidate(Request $request, Model $model): array
    {
        $key = $model->exists ? 'update' : 'create';

        $rules = array_map(
            static function (array|Closure $rule) use ($request, $model): array {
                return is_array($rule) ? $rule : call_user_func_array($rule, [$request, $model]);
            },
            Arr::only($this->rules, array_unique(['*', $key]))
        );

        return [$this->getKey() => Arr::flatten($rules, 1)];
    }
}
