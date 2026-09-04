@props([
    'name',
    'id' => null,
    'type' => 'text',
    'value' => null,
    'icon' => null,
])

@php
    $inputId = $id ?? $name;
    $hasError = $errors->has($name);
    $currentValue = $type === 'password' ? null : old($name, $value);
@endphp

<div @class(['position-relative' => $icon])>
    @if ($icon)
        <i class="bi {{ $icon }} settings-input-icon" aria-hidden="true"></i>
    @endif

    <input
        id="{{ $inputId }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($currentValue !== null) value="{{ $currentValue }}" @endif
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @endif
        {{ $attributes->class(['form-control settings-control', 'is-invalid' => $hasError]) }}
    >
</div>
