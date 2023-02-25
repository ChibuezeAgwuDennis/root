<?php

namespace Cone\Root\Http\Requests;

use Cone\Root\Widgets\Widget;

class WidgetRequest extends RootRequest
{
    /**
     * Get the widget bound to the request.
     */
    public function widget(): Widget
    {
        return $this->resolved();
    }
}
