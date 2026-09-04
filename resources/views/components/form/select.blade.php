@props([
    'name',
    'id' => null,
    'selected' => null,
])

@php
    $selectId = $id ?? $name;
    $hasError = $errors->has($name);
@endphp

<select
    id="{{ $selectId }}"
    name="{{ $name }}"
    @if ($hasError) aria-invalid="true" aria-describedby="{{ $selectId }}-error" @endif
    {{ $attributes->class(['form-select settings-control', 'is-invalid' => $hasError]) }}
>
    {{ $slot }}
</select>
