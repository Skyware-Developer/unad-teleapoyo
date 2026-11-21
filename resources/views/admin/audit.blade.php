@extends('layouts.app')
@section('title','Bitácora')
@section('content')
<div class="card"><h3>Bitácora de auditoría</h3>
 <table><tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Entidad</th><th>IP</th></tr>
 @foreach($logs as $l)<tr><td>{{ $l->created_at }}</td><td>{{ $l->user?->email ?? '—' }}</td><td>{{ $l->action }}</td><td>{{ $l->entity }} {{ $l->entity_id }}</td><td>{{ $l->ip }}</td></tr>@endforeach</table>
 {{ $logs->links() }}
</div>
@endsection
