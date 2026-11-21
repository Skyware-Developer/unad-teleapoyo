<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('requester_id')->constrained('users');
            $t->foreignId('specialty_id')->constrained('specialties');
            $t->text('patient_document');          // cifrado a nivel de atributo (cast 'encrypted')
            $t->text('reason');
            $t->text('clinical_summary');
            $t->string('status')->default('pending')->index(); // pending | answered
            $t->timestamps();
        });

        Schema::create('responses', function (Blueprint $t) {
            $t->id();
            // RF-07: restricción de unicidad = una única respuesta por consulta
            $t->foreignId('consultation_id')->unique()->constrained('consultations');
            $t->foreignId('specialist_id')->constrained('users');
            $t->text('guidance');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responses');
        Schema::dropIfExists('consultations');
    }
};
