<?php

namespace Cone\Root\Interfaces\Support\Collections;

use Cone\Root\Http\Requests\RootRequest;

interface Resources
{
    /**
     * Filter the available resources.
     *
     * @return \Cone\Root\Interfaces\Support\Collections\Resources
     */
    public function available(RootRequest $request): static;
}
