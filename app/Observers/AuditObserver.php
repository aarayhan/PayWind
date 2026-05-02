<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    public function created(Model $model): void
    {
        $this->writeLog($model, 'created', null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $oldValues = [];
        $newValues = [];

        foreach ($model->getChanges() as $key => $value) {
            if (in_array($key, ['updated_at', 'created_at'], true)) {
                continue;
            }

            $oldValues[$key] = $model->getOriginal($key);
            $newValues[$key] = $value;
        }

        if ($newValues === []) {
            return;
        }

        $this->writeLog($model, 'updated', $oldValues, $newValues);
    }

    public function deleted(Model $model): void
    {
        $this->writeLog($model, 'deleted', $model->getOriginal(), null);
    }

    protected function writeLog(Model $model, string $event, ?array $oldValues, ?array $newValues): void
    {
        AuditLog::query()->create([
            'user_id' => Auth::id(),
            'event' => $event,
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()?->ip(),
        ]);
    }
}
