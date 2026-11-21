<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Site;
use App\Models\Specialty;

/** Perfil coordinador: RF-10 (indicadores) y RF-12 (exportar CSV). */
class IndicatorController extends Controller
{
    public function index()
    {
        return view('indicators.index', $this->stats());
    }

    public function export()
    {
        $s = $this->stats();

        return response()->streamDownload(function () use ($s) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Indicador', 'Valor']);
            foreach ($s['summary'] as $k => $v) {
                fputcsv($out, [$k, $v]);
            }
            fputcsv($out, []);
            fputcsv($out, ['Especialidad', 'Consultas', 'Respondidas']);
            foreach ($s['bySpecialty'] as $r) {
                fputcsv($out, [$r['name'], $r['total'], $r['answered']]);
            }
            fputcsv($out, []);
            fputcsv($out, ['Sede', 'Consultas', 'Respondidas']);
            foreach ($s['bySite'] as $r) {
                fputcsv($out, [$r['name'], $r['total'], $r['answered']]);
            }
            fclose($out);
        }, 'indicadores_teleapoyo.csv', ['Content-Type' => 'text/csv']);
    }

    private function stats(): array
    {
        $all = Consultation::with(['response', 'requester'])->get();
        $answered = $all->filter(fn ($c) => $c->status === 'answered');
        $hours = $answered->map(fn ($c) => $c->created_at->diffInMinutes($c->response->created_at) / 60);

        $summary = [
            'Consultas realizadas' => $all->count(),
            'Consultas respondidas' => $answered->count(),
            'Consultas sin respuesta' => $all->count() - $answered->count(),
            '% respondidas' => $all->count() ? round($answered->count() / $all->count() * 100, 1) : 0,
            'Tiempo promedio de respuesta (h)' => $hours->count() ? round($hours->avg(), 1) : 0,
        ];

        $bySpecialty = Specialty::orderBy('name')->get()->map(function ($sp) use ($all) {
            $g = $all->where('specialty_id', $sp->id);

            return ['name' => $sp->name, 'total' => $g->count(), 'answered' => $g->where('status', 'answered')->count()];
        })->filter(fn ($r) => $r['total'] > 0)->values();

        $bySite = Site::orderBy('name')->get()->map(function ($si) use ($all) {
            $g = $all->filter(fn ($c) => $c->requester && $c->requester->site_id === $si->id);

            return ['name' => $si->name, 'total' => $g->count(), 'answered' => $g->where('status', 'answered')->count()];
        })->filter(fn ($r) => $r['total'] > 0)->values();

        return compact('summary', 'bySpecialty', 'bySite');
    }
}
