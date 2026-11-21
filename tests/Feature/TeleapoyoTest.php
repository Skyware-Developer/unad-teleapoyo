<?php

namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/** Cubre CP-01 a CP-10 de la matriz IEEE 829 del documento maestro (parcialmente automatizados). */
class TeleapoyoTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, array $extra = []): User
    {
        static $n = 0;
        $n++;

        // fresh(): recarga el modelo para incluir los valores por defecto de la base (p. ej., active = true)
        return User::create(array_merge([
            'name' => "U$n", 'email' => "u$n@t.test", 'password' => 'Password1234', 'role' => $role,
        ], $extra))->fresh();
    }

    private function consultation(User $requester, Specialty $sp, string $doc = '123456789'): Consultation
    {
        return Consultation::create([
            'requester_id' => $requester->id, 'specialty_id' => $sp->id, 'patient_document' => $doc,
            'reason' => 'Motivo', 'clinical_summary' => 'Resumen', 'status' => 'pending',
        ]);
    }

    public function test_login_valido_e_invalido(): void // CP-01, CP-02
    {
        $u = $this->user('general');
        $this->post('/login', ['email' => $u->email, 'password' => 'Password1234'])->assertRedirect('/home');
        $this->post('/logout');
        $this->post('/login', ['email' => $u->email, 'password' => 'mala'])->assertSessionHasErrors('email');
    }

    public function test_usuario_inactivo_no_ingresa(): void
    {
        $u = $this->user('general', ['active' => false]);
        $this->post('/login', ['email' => $u->email, 'password' => 'Password1234'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_acceso_por_rol(): void // CP-03
    {
        $this->actingAs($this->user('general'))->get('/admin/users')->assertStatus(403);
        $this->actingAs($this->user('admin'))->get('/admin/users')->assertOk();
    }

    public function test_registro_de_consulta_y_validacion(): void // CP-04, CP-05
    {
        $sp = Specialty::create(['name' => 'Cardiología']);
        $g = $this->user('general');
        $this->actingAs($g)->post('/consultations', ['specialty_id' => $sp->id, 'reason' => 'x', 'clinical_summary' => 'y'])
            ->assertSessionHasErrors('patient_document');
        $this->actingAs($g)->post('/consultations', ['patient_document' => '55555555', 'specialty_id' => $sp->id, 'reason' => 'x', 'clinical_summary' => 'y'])
            ->assertRedirect();
        $this->assertDatabaseCount('consultations', 1);
    }

    public function test_documento_del_paciente_se_guarda_cifrado(): void // CP-08 / RNF-02
    {
        $sp = Specialty::create(['name' => 'Cardiología']);
        $c = $this->consultation($this->user('general'), $sp, '987654321');
        $raw = DB::table('consultations')->where('id', $c->id)->value('patient_document');
        $this->assertStringNotContainsString('987654321', $raw);
        $this->assertSame('987654321', $c->fresh()->patient_document);
    }

    public function test_especialista_solo_ve_su_especialidad(): void // CP-06
    {
        $card = Specialty::create(['name' => 'Cardiología']);
        $ped = Specialty::create(['name' => 'Pediatría']);
        $s = $this->user('specialist', ['specialty_id' => $card->id]);
        $own = $this->consultation($this->user('general'), $card);
        $other = $this->consultation($this->user('general'), $ped);
        $this->actingAs($s)->get("/inbox/{$own->id}")->assertOk();
        $this->actingAs($s)->get("/inbox/{$other->id}")->assertStatus(403);
    }

    public function test_unica_respuesta_por_consulta(): void // CP-07
    {
        $sp = Specialty::create(['name' => 'Cardiología']);
        $s = $this->user('specialist', ['specialty_id' => $sp->id]);
        $c = $this->consultation($this->user('general'), $sp);

        $this->actingAs($s)->post("/inbox/{$c->id}/respond", ['guidance' => 'Primera'])->assertRedirect();
        $this->actingAs($s)->post("/inbox/{$c->id}/respond", ['guidance' => 'Segunda'])->assertStatus(409);

        $this->assertDatabaseCount('responses', 1);
        $this->assertSame('answered', $c->fresh()->status);
    }

    public function test_indicadores_para_coordinador(): void // CP-09
    {
        $sp = Specialty::create(['name' => 'Cardiología']);
        $this->consultation($this->user('general'), $sp);
        $this->actingAs($this->user('coordinator'))->get('/indicators')->assertOk()->assertSee('Consultas realizadas');
        $this->actingAs($this->user('general'))->get('/indicators')->assertStatus(403);
    }

    public function test_medico_general_solo_ve_sus_consultas(): void
    {
        $sp = Specialty::create(['name' => 'Cardiología']);
        $c = $this->consultation($this->user('general'), $sp);
        $this->actingAs($this->user('general'))->get("/consultations/{$c->id}")->assertStatus(403);
    }
}
