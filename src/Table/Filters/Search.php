<?php

namespace Cone\Root\Table\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Search extends Filter
{
    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, Builder $query, mixed $value): Builder
    {
        return $query;
    }

    /**
     * Get the value of the filter.
     */
    public function getValue(Request $request): mixed
    {
        return $request->input($this->getRequestKey());
    }

    /**
     * {@inheritdoc}
     */
    public function toField(FilterForm $form): SearchField
    {
        return SearchField::make($form, $this->getName(), $this->getRequestKey())
            ->value(function (Request $request, Model $model): ?string {
                return $model->getAttribute($this->getKey());
            })
            ->placeholder($this->getName().'...');
    }
}