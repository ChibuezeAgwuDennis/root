<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Form\Form;

class Hidden extends Field
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.hidden';

    /**
     * Create a new field instance.
     */
    public function __construct(Form $form, string $label, string $modelAttribute = null)
    {
        parent::__construct($form, $label, $modelAttribute);

        $this->type('hidden');
    }
}