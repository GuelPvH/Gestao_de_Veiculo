<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\ServiceStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class PublicServiceController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ServiceResource::collection(
            Service::query()
                ->where('status', ServiceStatus::Published)
                ->where('published_at', '<=', now())
                ->orderBy('sort_order')
                ->get(),
        );
    }

    public function show(Service $service): ServiceResource
    {
        abort_unless(
            $service->status === ServiceStatus::Published
            && $service->published_at?->isPast(),
            404,
        );

        return new ServiceResource($service);
    }
}
