<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;

class Dropdown extends Select
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.dropdown';

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, string $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->setAttribute('class', 'form-control combobox__control');
    }

    /**
     * {@inheritdoc}
     */
    public function toFormComponent(Request $request, Model $model): array
    {
        $data = parent::toFormComponent($request, $model);

        return array_merge($data, [
            'options' => array_map(static function (array $option): array {
                return array_merge($option, [
                    'html' => View::make('root::fields.dropdown-option', $option)->render(),
                ]);
            }, $data['options']),
            'selection' => Collection::make($data['options'])
                ->filter(fn (array $option): bool => $option['selected'] ?? false)
                ->map(static function (array $option): array {
                    return array_merge($option, [
                        'html' => View::make('root::fields.dropdown-option', $option)->render(),
                    ]);
                })
                ->values()
                ->all(),
            'config' => [
                'multiple' => $this->getAttribute('multiple'),
            ],
        ]);
    }
}