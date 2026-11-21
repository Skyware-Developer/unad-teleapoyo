<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('role')->default('general')->index(); // admin | coordinator | specialist | general
            $t->foreignId('specialty_id')->nullable()->constrained('specialties')->nullOnDelete();
            $t->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $t->boolean('active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropConstrainedForeignId('specialty_id');
            $t->dropConstrainedForeignId('site_id');
            $t->dropColumn(['role', 'active']);
        });
    }
};
