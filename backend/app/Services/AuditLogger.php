<?php

namespace App\Services;

use App\Models\AuditEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditLogger
{
    public static function record(Request $request, string $action, Model $model, ?array $before = null, ?array $after = null): AuditEvent
    {
        return AuditEvent::create([
            'actor_id' => $request->user()?->id,
            'action' => $action,
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'before' => self::sanitize($before),
            'after' => self::sanitize($after),
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 1000),
        ]);
    }

    private static function sanitize(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        unset($data['password'], $data['remember_token']);

        return $data;
    }
}
