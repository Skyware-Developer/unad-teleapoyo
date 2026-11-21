@extends('layouts.app')
@section('title','Catálogos')
@section('content')
<div class="grid" style="grid-template-columns:1fr 1fr;align-items:start">
 <div class="card"><h3>Especialidades</h3>
  <table>@foreach($specialties as $s)<tr><td>{{ $s->name }}</td><td>{{ $s->active ? 'Activa' : 'Inactiva' }}</td>
   <td><form method="post" action="{{ route('admin.specialties.toggle',$s) }}">@csrf<button class="btn sm alt">{{ $s->active ? 'Desactivar' : 'Activar' }}</button></form></td></tr>@endforeach</table>
  <form method="post" action="{{ route('admin.specialties.store') }}">@csrf<label>Nueva especialidad</label><input name="name" required><p><button class="btn sm">Agregar</button></p></form>
 </div>
 <div class="card"><h3>Sedes</h3>
  <table>@foreach($sites as $s)<tr><td>{{ $s->name }}</td><td>{{ $s->municipality }}</td></tr>@endforeach</table>
  <form method="post" action="{{ route('admin.sites.store') }}">@csrf<label>Nombre</label><input name="name" required><label>Municipio</label><input name="municipality" required><p><button class="btn sm">Agregar</button></p></form>
 </div>
</div>
@endsection
