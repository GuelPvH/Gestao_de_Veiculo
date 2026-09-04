@props([
    'id',
    'label',
    'name' => null,
    'help' => null,
    'required' => false,
])

@php($error = $name ? $errors->first($name) : null)

<div {{ $attributes->class(['form-field', 'has-error' => $error]) }}>
    <label for="{{ $id }}" class="form-field-label">
        {{ $label }}
        @if ($required)
            <span class="text-danger" aria-hidden="true">*</span>
            <span class="visually-hidden">(obrigatório)</span>
        @endif
        @if ($help)
            <small id="{{ $id }}-help">{{ $help }}</small>
        @endif
    </label>

    <div class="form-field-control">
        {{ $slot }}
    </div>

    @if ($error)
        <div id="{{ $id }}-error" class="invalid-feedback d-block">{{ $error }}</div>
    @endif
</div>
