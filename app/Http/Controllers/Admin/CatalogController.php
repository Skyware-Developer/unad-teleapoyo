<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Site;
use App\Models\Specialty;
use Illuminate\Http\Request;

/** RF-03: especialidades y sedes. */
class CatalogController extends Controller
{
    public function index()
    {
        return view('admin.catalogs', [
            'specialties' => Specialty::orderBy('name')->get(),
            'sites' => Site::orderBy('name')->get(),
        ]);
    }

    public function storeSpecialty(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100', 'unique:specialties,name']]);
        $s = Specialty::create($data);
        AuditLog::record('specialty_created', 'specialty', $s->id);

        return back()->with('ok', 'Especialidad creada.');
    }

    public function toggleSpecialty(Specialty $specialty)
    {
        $specialty->update(['active' => ! $specialty->active]);
        AuditLog::record('specialty_toggled', 'specialty', $specialty->id);

        return back()->with('ok', 'Especialidad actualizada.');
    }

    public function storeSite(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:sites,name'],
            'municipality' => ['required', 'string', 'max:100'],
        ]);
        $s = Site::create($data);
        AuditLog::record('site_created', 'site', $s->id);

        return back()->with('ok', 'Sede creada.');
    }
}
