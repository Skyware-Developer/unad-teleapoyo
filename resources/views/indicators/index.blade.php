@extends('layouts.app')
@section('title','Indicadores')
@section('content')
<div class="grid">@foreach($summary as $k=>$v)<div class="kpi">{{ $k }}<b>{{ $v }}</b></div>@endforeach</div>
<p><a class="btn" href="{{ route('indicators.export') }}">Exportar CSV</a></p>
<div class="grid" style="grid-template-columns:1fr 1fr;align-items:start">
 <div class="card"><h3>Por especialidad</h3><table><tr><th>Especialidad</th><th>Consultas</th><th>Respondidas</th></tr>
  @foreach($bySpecialty as $r)<tr><td>{{ $r['name'] }}</td><td>{{ $r['total'] }}</td><td>{{ $r['answered'] }}</td></tr>@endforeach</table></div>
 <div class="card"><h3>Por sede</h3><table><tr><th>Sede</th><th>Consultas</th><th>Respondidas</th></tr>
  @foreach($bySite as $r)<tr><td>{{ $r['name'] }}</td><td>{{ $r['total'] }}</td><td>{{ $r['answered'] }}</td></tr>@endforeach</table></div>
</div>
@endsection
