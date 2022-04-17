<?php

namespace Cone\Root\Filters;

use Cone\Root\Fields\Field;
use Cone\Root\Fields\Relation;
use Cone\Root\Support\Collections\Fields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Search extends Filter
{
    /**
     * The searchable fields.
     *
     * @var \Cone\Root\Support\Collections\Fields
     */
    protected Fields $fields;

    /**
     * Create a new filter instance.
     *
     * @param  \Cone\Root\Support\Collections\Fields  $fields
     * @return void
     */
    public function __construct(Fields $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Apply the filter on the query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, Builder $query, mixed $value): Builder
    {
        if (empty($value)) {
            return $query;
        }

        $attributes = $this->mapColumns();

        foreach ($attributes as $attribute => $columns) {
            $boolean = array_key_first($attributes) === $attribute ? 'and' : 'or';

            if (is_array($columns)) {
                $query->has($attribute, '>=', 1, $boolean, static function (Builder $query) use ($columns, $value): Builder {
                    foreach ($columns as $column) {
                        $boolean = $columns[0] === $column ? 'and' : 'or';

                        $query->where($query->qualifyColumn($column), 'like', "%{$value}%", $boolean);
                    }

                    return $query;
                });
            } else {
                $query->where($query->qualifyColumn($attribute), 'like', "%{$value}%", $boolean);
            }
        }

        return $query;
    }

    /**
     * Map the searchable columns.
     *
     * @return array
     */
    protected function mapColumns(): array
    {
        return $this->fields->mapWithKeys(static function (Field $field): array {
            return [
                $field->getKey() => $field instanceof Relation ? $field->getSearchableColumns() : null,
            ];
        })->toArray();
    }
}