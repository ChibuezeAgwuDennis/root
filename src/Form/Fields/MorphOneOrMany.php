<?php

namespace Cone\Root\Form\Fields;

use Illuminate\Database\Eloquent\Relations\MorphOneOrMany as EloquentRelation;

abstract class MorphOneOrMany extends HasOneOrMany
{
    /**
     * {@inheritdoc}
     */
    public function getRelation(): EloquentRelation
    {
        return parent::getRelation();
    }
}
