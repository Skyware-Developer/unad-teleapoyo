@extends('layouts.app')
@section('title','Ingreso')
@section('content')
<div class="card" style="max-width:420px;margin:2rem auto">
 <h2>Ingreso a Teleapoyo</h2>
 <form method="post" action="{{ url('/login') }}">@csrf
  <label>Correo</label><input type="email" name="email" value="{{ old('email') }}" required autofocus>
  <label>Contraseña</label><input type="password" name="password" required>
  <p><button class="btn">Ingresar</button></p>
 </form>
 <p class="aviso">Plataforma de teleapoyo entre profesionales de salud. No realiza diagnósticos ni prescripciones.</p>
</div>
@endsection
