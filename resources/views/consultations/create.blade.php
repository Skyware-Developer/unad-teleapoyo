@extends('layouts.app')
@section('title','Nueva consulta')
@section('content')
<div class="card">
 <h3>Nueva consulta de teleapoyo</h3>
 <div class="aviso">Incluya solo la información clínica estrictamente necesaria. No registre nombre, dirección ni teléfono del paciente. Esta plataforma ofrece orientación entre profesionales; no es diagnóstico ni prescripción y la conducta clínica es de su responsabilidad.</div>
 <form method="post" action="{{ route('consultations.store') }}">@csrf
  <label>N.º de documento del paciente</label><input name="patient_document" value="{{ old('patient_document') }}" required>
  <label>Especialidad</label><select name="specialty_id" required><option value="">Seleccione…</option>@foreach($specialties as $s)<option value="{{ $s->id }}" @selected(old('specialty_id')==$s->id)>{{ $s->name }}</option>@endforeach</select>
  <label>Motivo de la consulta</label><textarea name="reason" maxlength="500" required>{{ old('reason') }}</textarea>
  <label>Resumen clínico mínimo necesario</label><textarea name="clinical_summary" maxlength="2000" required>{{ old('clinical_summary') }}</textarea>
  <p><button class="btn">Enviar consulta</button></p>
 </form>
</div>
@endsection
