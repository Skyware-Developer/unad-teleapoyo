<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Consultation;
use App\Models\Response;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Perfil especialista: RF-06 (bandeja por especialidad) y RF-07 (única respuesta). */
class InboxController extends Controller
{
    public function index()
    {
        $consultations = Consultation::with(['requester.site', 'response'])
            ->where('specialty_id', auth()->user()->specialty_id)
            ->orderByRaw("case when status = 'pending' then 0 else 1 end")
            ->orderByDesc('id')
            ->get();

        return view('inbox.index', compact('consultations'));
    }

    public function show(Consultation $consultation)
    {
        $this->authorizeSpecialty($consultation);
        $consultation->load(['requester.site', 'specialty', 'response']);

        return view('inbox.show', compact('consultation'));
    }

    public function respond(Request $request, Consultation $consultation)
    {
        $this->authorizeSpecialty($consultation);
        $data = $request->validate(['guidance' => ['required', 'string', 'max:3000']]);

        try {
            DB::transaction(function () use ($consultation, $data) {
                $locked = Consultation::lockForUpdate()->findOrFail($consultation->id);
                if ($locked->response()->exists()) {
                    abort(409, 'Esta consulta ya fue respondida.');
                }
                $response = Response::create([
                    'consultation_id' => $locked->id,
                    'specialist_id' => auth()->id(),
                    'guidance' => $data['guidance'],
                ]);
                $locked->update(['status' => 'answered']);
                AuditLog::record('response_created', 'response', $response->id);
            });
        } catch (QueryException $e) {
            // La restricción única de la base de datos es la última barrera (RF-07).
            return redirect()->route('inbox.show', $consultation)->withErrors(['guidance' => 'Esta consulta ya fue respondida.']);
        }

        return redirect()->route('inbox.show', $consultation)->with('ok', 'Respuesta enviada.');
    }

    private function authorizeSpecialty(Consultation $consultation): void
    {
        abort_unless($consultation->specialty_id === auth()->user()->specialty_id, 403);
    }
}
