<?php

namespace Cone\Root\Fields;

class Text extends Field
{
    /**
     * The field attributes.
     *
     * @var array
     */
    protected array $attributes = [
        'type' => 'text',
    ];

    /**
     * Set the size attribute.
     *
     * @param  int  $value
     * @return $this
     */
    public function size(int $value): self
    {
        return $this->setAttribute('size', $value);
    }

    /**
     * Set the minlength attribute.
     *
     * @param  int  $value
     * @return $this
     */
    public function minlength(int $value): self
    {
        return $this->setAttribute('minlength', $value);
    }

    /**
     * Set the maxlength attribute.
     *
     * @param  int  $value
     * @return $this
     */
    public function maxlength(int $value): self
    {
        return $this->setAttribute('maxlength', $value);
    }

    /**
     * Set the placeholder attribute.
     *
     * @param  string  $value
     * @return $this
     */
    public function placeholder(string $value): self
    {
        return $this->setAttribute('placeholder', $value);
    }

    /**
     * Set the type attribute.
     *
     * @param  string  $value
     * @return $this
     */
    public function type(string $value): self
    {
        return $this->setAttribute('type', $value);
    }
}
