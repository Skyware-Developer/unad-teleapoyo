<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Consultation;
use App\Models\Specialty;
use Illuminate\Http\Request;

/** Perfil médico general: RF-04, RF-05, RF-08, RF-09. */
class ConsultationController extends Controller
{
    public function index()
    {
        $consultations = Consultation::with(['specialty', 'response'])
            ->where('requester_id', auth()->id())
            ->orderByDesc('id')
            ->get();

        return view('consultations.index', compact('consultations'));
    }

    public function create()
    {
        return view('consultations.create', ['specialties' => Specialty::where('active', true)->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_document' => ['required', 'regex:/^[0-9A-Za-z]{5,20}$/'],
            'specialty_id' => ['required', 'exists:specialties,id,active,1'],
            'reason' => ['required', 'string', 'max:500'],
            'clinical_summary' => ['required', 'string', 'max:2000'],
        ]);

        $consultation = Consultation::create($data + [
            'requester_id' => auth()->id(),
            'status' => 'pending',
        ]);
        AuditLog::record('consultation_created', 'consultation', $consultation->id);

        return redirect()->route('consultations.show', $consultation)->with('ok', 'Consulta enviada.');
    }

    public function show(Consultation $consultation)
    {
        abort_unless($consultation->requester_id === auth()->id(), 403);
        $consultation->load(['specialty', 'response.specialist']);

        return view('consultations.show', compact('consultation'));
    }
}
