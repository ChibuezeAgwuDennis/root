<?php

namespace Cone\Root\Fields;

use Closure;
use DateTimeInterface;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date as DateFactory;

class Date extends Field
{
    /**
     * The date format.
     */
    protected string $format = 'Y-m-d';

    /**
     * The timezone.
     */
    protected string $timezone;

    /**
     * Indicates if the field should include time.
     */
    protected bool $withTime = false;

    /**
     * The default timezone.
     */
    protected static ?string $defaultTimezone = null;

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, Closure|string|null $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->type('date');
        $this->step(1);
        $this->timezone(static::$defaultTimezone ?: Config::get('app.timezone'));
    }

    /**
     * Set the default timezone.
     */
    public static function defaultTimezone(string|DateTimeZone $value): void
    {
        static::$defaultTimezone = $value instanceof DateTimeZone ? $value->getName() : $value;
    }

    /**
     * Set the "min" HTML attribute.
     */
    public function min(string|DateTimeInterface $value): static
    {
        return $this->setAttribute('min', is_string($value) ? $value : $value->format('Y-m-d'));
    }

    /**
     * Set the "max" HTML attribute.
     */
    public function max(string|DateTimeInterface $value): static
    {
        return $this->setAttribute('max', is_string($value) ? $value : $value->format('Y-m-d'));
    }

    /**
     * Set the "step" HTML attribute.
     */
    public function step(int $value): static
    {
        return $this->setAttribute('step', $value);
    }

    /**
     * Set the with time attribute.
     */
    public function withTime(bool $value = true): static
    {
        $this->withTime = $value;

        $this->format = $value ? 'Y-m-d H:i:s' : 'Y-m-d';

        $this->type($value ? 'datetime-local' : 'date');

        return $this;
    }

    /**
     * Set the timezone.
     */
    public function timezone(string|DateTimeZone $value): static
    {
        $this->timezone = $value instanceof DateTimeZone ? $value->getName() : $value;

        $this->suffix($this->timezone);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueForHydrate(Request $request): ?string
    {
        $value = parent::getValueForHydrate($request);

        if (! is_null($value)) {
            $value = DateFactory::parse($value, $this->timezone)
                ->setTimezone(Config::get('app.timezone'))
                ->toISOString();
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(Model $model): mixed
    {
        $value = parent::getValue($model);

        if (! is_null($value)) {
            $value = DateFactory::parse($value, Config::get('app.timezone'))
                ->setTimezone($this->timezone);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveFormat(Request $request, Model $model): ?string
    {
        if (is_null($this->formatResolver)) {
            $this->formatResolver = function (Request $request, Model $model, mixed $value): ?string {
                return is_null($value) ? $value : $value->format($this->format);
            };
        }

        return parent::resolveFormat($request, $model);
    }
}
