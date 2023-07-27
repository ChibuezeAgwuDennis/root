<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Form\Form;

class Color extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct(Form $form, string $label, string $key = null)
    {
        parent::__construct($form, $label, $key);

        $this->type('color');
    }
}
