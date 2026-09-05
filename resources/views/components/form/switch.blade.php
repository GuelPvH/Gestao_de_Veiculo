@props([
    'name',
    'id' => null,
    'label',
    'checked' => false,
    'disabled' => false,
])

@php($switchId = $id ?? $name)

<div class="form-check form-switch m-0">
    <input type="hidden" name="{{ $name }}" value="0" @disabled($disabled)>
    <input
        id="{{ $switchId }}"
        name="{{ $name }}"
        class="form-check-input"
        type="checkbox"
        role="switch"
        value="1"
        @checked((bool) old($name, $checked))
        @disabled($disabled)
        aria-label="{{ $label }}"
    >
</div>
