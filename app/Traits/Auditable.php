<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            static::logAudit('create', $model, null, $model->getAttributes());
        });

        static::updated(function (Model $model) {
            $original = $model->getOriginal();
            $changes = $model->getChanges();
            
            if (!empty($changes)) {
                static::logAudit('update', $model, $original, $changes);
            }
        });

        static::deleted(function (Model $model) {
            static::logAudit('delete', $model, $model->getAttributes(), null);
        });
    }

    protected static function logAudit($action, Model $model, $oldValues, $newValues)
    {
        $userId = auth()->id();
        
        if ($userId === null) {
            return;
        }

        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => class_basename($model),
            'entity_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
