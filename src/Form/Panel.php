<?php

namespace Cone\Root\Form;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Stringable;

class Panel implements Htmlable, Stringable
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
     * Render the HTML string.
     */
    public function toHtml(): string
    {
        return $this->render()->render();
    }

    /**
     * Convert the panel to a string.
     */
    public function __toString(): string
    {
        return $this->toHtml();
    }
}