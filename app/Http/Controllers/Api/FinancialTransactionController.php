<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\ApproveFinancialTransaction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListFinancialTransactionsRequest;
use App\Http\Requests\StoreFinancialTransactionRequest;
use App\Http\Requests\UpdateFinancialTransactionRequest;
use App\Http\Resources\FinancialTransactionResource;
use App\Models\FinancialTransaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

final class FinancialTransactionController extends Controller
{
    public function index(ListFinancialTransactionsRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $transactions = FinancialTransaction::query()
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['project_id'] ?? null, fn ($query, $projectId) => $query->where('project_id', $projectId))
            ->when($filters['client_id'] ?? null, fn ($query, $clientId) => $query->where('client_id', $clientId))
            ->when($filters['category_id'] ?? null, fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('due_date', '>=', $date))
            ->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('due_date', '<=', $date))
            ->latest('id')
            ->paginate($filters['per_page'] ?? 15);

        return FinancialTransactionResource::collection($transactions);
    }

    public function store(StoreFinancialTransactionRequest $request): JsonResponse
    {
        $transaction = new FinancialTransaction($request->validated());
        $transaction->setAttribute('created_by', $request->user()?->getAuthIdentifier());
        $transaction->save();

        return (new FinancialTransactionResource($transaction))->response()->setStatusCode(201);
    }

    public function show(FinancialTransaction $transaction): FinancialTransactionResource
    {
        Gate::authorize('view', $transaction);

        return new FinancialTransactionResource($transaction);
    }

    public function update(
        UpdateFinancialTransactionRequest $request,
        FinancialTransaction $transaction,
    ): FinancialTransactionResource {
        $transaction->update($request->validated());

        return new FinancialTransactionResource($transaction->refresh());
    }

    public function destroy(FinancialTransaction $transaction): Response
    {
        Gate::authorize('delete', $transaction);
        $transaction->delete();

        return response()->noContent();
    }

    public function approve(
        \Illuminate\Http\Request $request,
        FinancialTransaction $transaction,
        ApproveFinancialTransaction $approveTransaction,
    ): FinancialTransactionResource {
        Gate::authorize('approve', $transaction);

        $user = $request->user();
        abort_unless($user instanceof User, 401);

        return new FinancialTransactionResource(
            $approveTransaction->handle($transaction, $user),
        );
    }
}
