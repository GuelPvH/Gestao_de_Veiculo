<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicStoreLeadRequest;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;

final class PublicLeadController extends Controller
{
    public function store(PublicStoreLeadRequest $request): JsonResponse
    {
        $lead = Lead::query()->create($request->validated());

        return (new LeadResource($lead))->response()->setStatusCode(201);
    }
}
