@extends('layouts.app')
@section('title','Consulta')
@section('content')
<div class="card">
 <h3>Consulta n.º {{ $consultation->id }} <span class="tag {{ $consultation->display_status }}">{{ $consultation->display_status }}</span></h3>
 <p><b>Solicitante:</b> {{ $consultation->requester->name }} ({{ $consultation->requester->site?->name }}) · <b>Documento:</b> {{ $consultation->patient_document }}</p>
 <p><b>Motivo:</b><br>{{ $consultation->reason }}</p>
 <p><b>Resumen clínico:</b><br>{!! nl2br(e($consultation->clinical_summary)) !!}</p>
</div>
<div class="card">
 @if($consultation->response)
  <h3>Respuesta emitida</h3><p>{!! nl2br(e($consultation->response->guidance)) !!}</p>
  <p class="aviso">Esta consulta ya tiene su única respuesta; no admite otra.</p>
 @else
  <h3>Respuesta de orientación (única)</h3>
  <div class="aviso">Ofrezca orientación o consejería. No prescriba medicamentos ni genere órdenes médicas por este medio.</div>
  <form method="post" action="{{ route('inbox.respond',$consultation) }}">@csrf
   <textarea name="guidance" maxlength="3000" required>{{ old('guidance') }}</textarea>
   <p><button class="btn">Enviar respuesta</button></p>
  </form>
 @endif
</div>
@endsection
