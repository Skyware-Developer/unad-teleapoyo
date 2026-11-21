@extends('layouts.app')
@section('title','Bandeja')
@section('content')
<div class="card"><h3>Bandeja de {{ auth()->user()->specialty?->name }}</h3>
 <table><tr><th>N.º</th><th>Fecha</th><th>Sede solicitante</th><th>Motivo</th><th>Estado</th><th></th></tr>
 @forelse($consultations as $c)
 <tr><td>{{ $c->id }}</td><td>{{ $c->created_at->format('d/m/Y H:i') }}</td><td>{{ $c->requester->site?->name }}</td><td>{{ \Illuminate\Support\Str::limit($c->reason,60) }}</td>
  <td><span class="tag {{ $c->display_status }}">{{ $c->display_status }}</span></td>
  <td><a class="btn sm" href="{{ route('inbox.show',$c) }}">{{ $c->status==='answered' ? 'Ver' : 'Responder' }}</a></td></tr>
 @empty<tr><td colspan="6">No hay consultas para su especialidad.</td></tr>@endforelse
 </table></div>
@endsection
