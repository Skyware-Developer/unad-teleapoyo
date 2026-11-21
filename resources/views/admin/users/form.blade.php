@extends('layouts.app')
@section('title','Usuario')
@section('content')
<div class="card">
 <h3>{{ $user->exists ? 'Editar usuario' : 'Nuevo usuario' }}</h3>
 <form method="post" action="{{ $user->exists ? route('admin.users.update',$user) : route('admin.users.store') }}">@csrf
  @if($user->exists) @method('PUT') @endif
  <label>Nombre</label><input name="name" value="{{ old('name',$user->name) }}" required>
  <label>Correo</label><input type="email" name="email" value="{{ old('email',$user->email) }}" required>
  <label>Contraseña {{ $user->exists ? '(dejar vacío para no cambiar)' : '(mínimo 10 caracteres)' }}</label><input type="password" name="password" {{ $user->exists ? '' : 'required' }}>
  <label>Rol</label>
  <select name="role" required>@foreach(\App\Models\User::ROLES as $k=>$v)<option value="{{ $k }}" @selected(old('role',$user->role)===$k)>{{ $v }}</option>@endforeach</select>
  <label>Especialidad (solo especialistas)</label>
  <select name="specialty_id"><option value="">—</option>@foreach($specialties as $s)<option value="{{ $s->id }}" @selected(old('specialty_id',$user->specialty_id)==$s->id)>{{ $s->name }}</option>@endforeach</select>
  <label>Sede</label>
  <select name="site_id"><option value="">—</option>@foreach($sites as $s)<option value="{{ $s->id }}" @selected(old('site_id',$user->site_id)==$s->id)>{{ $s->name }}</option>@endforeach</select>
  <label><input type="checkbox" name="active" value="1" style="width:auto" @checked(old('active',$user->active))> Usuario activo</label>
  <p><button class="btn">Guardar</button> <a class="btn alt" href="{{ route('admin.users.index') }}">Cancelar</a></p>
 </form>
</div>
@endsection
