<?php

namespace Cone\Root\Form;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Stringable;

class Panel implements Stringable
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::form.panel';

    /**
     * The panel title.
     */
    protected string $title;

    /**
     * The panel fields.
     */
    protected array $fields = [];

    /**
     * Create a new controller instance.
     */
    public function __construct(string $title, array $fields = [])
    {
        $this->title = $title;
        $this->fields = $fields;
    }

    /**
     * Get the view data.
     */
    public function data(Request $request): array
    {
        return [
            'title' => $this->title,
            'fields' => $this->fields,
        ];
    }

    /**
     * Render the table.
     */
    public function render(): View
    {
        return App::make('view')->make(
            $this->template,
            App::call([$this, 'data'])
        );
    }

    /**
     * Convert the panel to a string.
     */
    public function __toString(): string
    {
        return $this->render()->render();
    }
}
