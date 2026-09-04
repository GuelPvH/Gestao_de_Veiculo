@props(['id', 'label', 'type' => 'text', 'placeholder' => ''])

<div class="mb-3">
    <label for="{{ $id }}" class="form-label text-dark small fw-medium mb-1">
        {{ $label }}
    </label>
    <input 
        type="{{ $type }}" 
        id="{{ $id }}" 
        name="{{ $id }}"
        class="form-control bg-gray-50 border-light-subtle py-2 px-3" 
        placeholder="{{ $placeholder }}" 
        {{ $attributes }}
    />
</div>