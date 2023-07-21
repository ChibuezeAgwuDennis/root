<div class="form-group--row">
    <label class="form-label" for="{{ $attrs->get('id') }}">{{ $label }}</label>
    <textarea {{ $attrs->class(['form-control', 'form-control--invalid' => $invalid]) }}>{{ $value }}</textarea>
    @if($invalid)
        <span class="field-feedback field-feedback--invalid">{!! $error !!}</span>
    @endif
    @if($help)
        <span class="form-description">{!! $help !!}</span>
    @endif
</div>
