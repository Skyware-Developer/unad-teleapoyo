@extends('layouts.app')
@section('title','Usuarios')
@section('content')
<div class="card">
 <p><a class="btn" href="{{ route('admin.users.create') }}">Nuevo usuario</a></p>
 <table>
  <tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Especialidad</th><th>Sede</th><th>Estado</th><th></th></tr>
  @foreach($users as $u)
  <tr><td>{{ $u->name }}</td><td>{{ $u->email }}</td><td>{{ $u->roleLabel() }}</td><td>{{ $u->specialty?->name }}</td><td>{{ $u->site?->name }}</td>
   <td>{{ $u->active ? 'Activo' : 'Inactivo' }}</td><td><a class="btn sm" href="{{ route('admin.users.edit',$u) }}">Editar</a></td></tr>
  @endforeach
 </table>
</div>
@endsection
