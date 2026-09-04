@props(['icon', 'title'])

<div class="col-md-6 col-lg-4 d-flex">
    <div class="bg-white rounded-3 shadow-sm border p-4 w-100">
        <div class="bg-primary bg-opacity-10 rounded-2 d-flex justify-content-center align-items-center mb-3" style="width: 48px; height: 48px;">
            <i class="{{ $icon }} text-primary fs-5"></i>
        </div>
        <h3 class="fs-5 fw-semibold text-dark">{{ $title }}</h3>
        <p class="text-secondary small mb-0 mt-2">{{ $slot }}</p>
    </div>
</div>