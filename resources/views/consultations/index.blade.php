@extends('layouts.app')
@section('title','Mis consultas')
@section('content')
<div class="card">
 <p><a class="btn" href="{{ route('consultations.create') }}">Nueva consulta</a></p>
 <table><tr><th>N.º</th><th>Fecha</th><th>Especialidad</th><th>Motivo</th><th>Estado</th><th></th></tr>
 @forelse($consultations as $c)
 <tr><td>{{ $c->id }}</td><td>{{ $c->created_at->format('d/m/Y H:i') }}</td><td>{{ $c->specialty->name }}</td><td>{{ \Illuminate\Support\Str::limit($c->reason,60) }}</td>
  <td><span class="tag {{ $c->display_status }}">{{ $c->display_status }}</span></td><td><a class="btn sm" href="{{ route('consultations.show',$c) }}">Ver</a></td></tr>
 @empty<tr><td colspan="6">Aún no ha registrado consultas.</td></tr>@endforelse
 </table>
</div>
@endsection
