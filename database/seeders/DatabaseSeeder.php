<?php

namespace Database\Seeders;

use App\Models\Consultation;
use App\Models\Response;
use App\Models\Site;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;

/** Datos FICTICIOS para desarrollo y pruebas. Cámbielos antes de cualquier uso real. */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $pw = 'Teleapoyo2026!';

        $sites = collect([['Hospital de Apartadó', 'Apartadó'], ['Centro de Salud Turbo', 'Turbo'], ['Puesto de Salud Carepa', 'Carepa']])
            ->map(fn ($s) => Site::create(['name' => $s[0], 'municipality' => $s[1]]));

        $specs = collect(['Medicina interna', 'Pediatría', 'Ginecología', 'Cardiología', 'Dermatología'])
            ->mapWithKeys(fn ($n) => [$n => Specialty::create(['name' => $n])]);

        User::create(['name' => 'Admin Demo', 'email' => 'admin@teleapoyo.test', 'password' => $pw, 'role' => 'admin']);
        User::create(['name' => 'Coordinador Demo', 'email' => 'coordinador@teleapoyo.test', 'password' => $pw, 'role' => 'coordinator']);
        $card = User::create(['name' => 'Dra. Cardiología Demo', 'email' => 'cardiologia@teleapoyo.test', 'password' => $pw, 'role' => 'specialist', 'specialty_id' => $specs['Cardiología']->id]);
        User::create(['name' => 'Dr. Pediatría Demo', 'email' => 'pediatria@teleapoyo.test', 'password' => $pw, 'role' => 'specialist', 'specialty_id' => $specs['Pediatría']->id]);
        $g1 = User::create(['name' => 'Médico General Turbo', 'email' => 'general1@teleapoyo.test', 'password' => $pw, 'role' => 'general', 'site_id' => $sites[1]->id]);
        $g2 = User::create(['name' => 'Médico General Carepa', 'email' => 'general2@teleapoyo.test', 'password' => $pw, 'role' => 'general', 'site_id' => $sites[2]->id]);

        $c1 = Consultation::create(['requester_id' => $g1->id, 'specialty_id' => $specs['Cardiología']->id, 'patient_document' => '1000000001',
            'reason' => 'Dolor torácico atípico en paciente hipertenso (caso ficticio).', 'clinical_summary' => 'Paciente de 58 años, HTA controlada. ECG sin cambios agudos. (Caso ficticio.)', 'status' => 'answered']);
        Response::create(['consultation_id' => $c1->id, 'specialist_id' => $card->id, 'guidance' => 'Sugiero ecocardiograma y prueba de esfuerzo antes de remitir; si hay cambios, remitir. (Caso ficticio.)']);
        Consultation::create(['requester_id' => $g2->id, 'specialty_id' => $specs['Cardiología']->id, 'patient_document' => '1000000002',
            'reason' => 'Soplo cardíaco nuevo (caso ficticio).', 'clinical_summary' => 'Adulto de 40 años asintomático. (Caso ficticio.)']);
        Consultation::create(['requester_id' => $g1->id, 'specialty_id' => $specs['Pediatría']->id, 'patient_document' => '1000000003',
            'reason' => 'Fiebre recurrente en lactante (caso ficticio).', 'clinical_summary' => 'Lactante de 10 meses, tres episodios en un mes. (Caso ficticio.)']);
    }
}
