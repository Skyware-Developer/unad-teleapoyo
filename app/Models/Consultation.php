<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Consultation extends Model
{
    protected $fillable = ['requester_id', 'specialty_id', 'patient_document', 'reason', 'clinical_summary', 'status'];

    // RNF-02: el único dato del paciente es el n.º de documento, cifrado en reposo.
    protected $casts = ['patient_document' => 'encrypted'];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function response(): HasOne
    {
        return $this->hasOne(Response::class);
    }

    /** Estado visible: Pendiente, Respondida o Vencida (sin respuesta pasado el plazo). */
    public function getDisplayStatusAttribute(): string
    {
        if ($this->status === 'answered') {
            return 'Respondida';
        }
        $limit = now()->subHours(config('teleapoyo.sla_hours'));

        return $this->created_at->lt($limit) ? 'Vencida' : 'Pendiente';
    }
}
