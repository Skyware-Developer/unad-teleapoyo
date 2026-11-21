<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'action', 'entity', 'entity_id', 'ip', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** RF-11 / RNF-03: registra quién hizo qué y cuándo. */
    public static function record(string $action, ?string $entity = null, ?int $entityId = null, ?int $userId = null): void
    {
        static::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'ip' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
