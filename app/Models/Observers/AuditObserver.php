<?php

declare(strict_types=1);

namespace App\Models\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final readonly class AuditObserver
{
    /** @var list<string> */
    private const REDACTED_FIELDS = [
        'password', 'remember_token', 'two_factor_secret',
        'two_factor_recovery_codes', 'token', 'token_hash',
    ];

    public function __construct(
        private Request $request,
    ) {}

    public function created(Model $model): void
    {
        $this->record($model, 'created', null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        $oldValues = array_intersect_key($model->getOriginal(), $changes);

        $this->record($model, 'updated', $oldValues, $changes);
    }

    public function deleted(Model $model): void
    {
        $this->record($model, 'deleted', $model->getOriginal(), null);
    }

    /**
     * @param array<string, mixed>|null $oldValues
     * @param array<string, mixed>|null $newValues
     */
    private function record(Model $model, string $action, ?array $oldValues, ?array $newValues): void
    {
        $requestId = $this->request->headers->get('X-Request-ID');

        AuditLog::query()->create([
            'user_id' => $this->request->user()?->getAuthIdentifier(),
            'action' => $action,
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => $model->getKey(),
            'old_values' => $this->sanitise($oldValues),
            'new_values' => $this->sanitise($newValues),
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'request_id' => is_string($requestId) && Str::isUuid($requestId)
                ? $requestId
                : (string) Str::uuid(),
        ]);
    }

    /**
     * @param array<string, mixed>|null $values
     * @return array<string, mixed>|null
     */
    private function sanitise(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        return collect($values)
            ->except([...self::REDACTED_FIELDS, 'created_at', 'updated_at'])
            ->all();
    }
}
