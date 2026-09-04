@props(['size' => 'sm'])

@php
    $isSmall = $size === 'sm';
    $widthHeight = $isSmall ? '40px' : '48px';
    $wrapperClasses = "bg-primary rounded d-flex justify-content-center align-items-center " . (!$isSmall ? 'shadow rounded-3' : '');
    $textClasses = "text-white fw-bold " . ($isSmall ? 'fs-5 tracking-logo-sm' : 'fs-4 tracking-logo-md');
@endphp

<div class="{{ $wrapperClasses }}" style="width: {{ $widthHeight }}; height: {{ $widthHeight }};">
    <span class="{{ $textClasses }}">D</span>
</div>