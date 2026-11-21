@extends('layouts.app')
@section('title','Consulta')
@section('content')
<div class="card">
 <h3>Consulta n.º {{ $consultation->id }} <span class="tag {{ $consultation->display_status }}">{{ $consultation->display_status }}</span></h3>
 <p><b>Especialidad:</b> {{ $consultation->specialty->name }} · <b>Documento:</b> {{ $consultation->patient_document }}</p>
 <p><b>Motivo:</b><br>{{ $consultation->reason }}</p>
 <p><b>Resumen clínico:</b><br>{!! nl2br(e($consultation->clinical_summary)) !!}</p>
</div>
<div class="card">
 <h3>Respuesta del especialista</h3>
 @if($consultation->response)
  <p>{!! nl2br(e($consultation->response->guidance)) !!}</p>
  <small>Dr(a). {{ $consultation->response->specialist->name }} · {{ $consultation->response->created_at->format('d/m/Y H:i') }}</small>
 @else <p>Aún sin respuesta.</p> @endif
 <div class="aviso">Orientación de teleapoyo (Resolución 1644 de 2026): no constituye diagnóstico ni prescripción. La decisión y la responsabilidad de la conducta clínica son del médico solicitante.</div>
</div>
@endsection
