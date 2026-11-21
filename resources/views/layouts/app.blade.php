<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Teleapoyo') · Teleapoyo</title>
<style>
 :root{--navy:#1f3a5f;--teal:#2a7f8e;--bg:#f4f6f8;--line:#d5dbe1}
 *{box-sizing:border-box} body{margin:0;font-family:system-ui,Segoe UI,Arial,sans-serif;background:var(--bg);color:#1c2733}
 header{background:var(--navy);color:#fff;padding:.7rem 1.2rem;display:flex;gap:1rem;align-items:center;flex-wrap:wrap}
 header a{color:#fff;text-decoration:none;margin-right:.9rem} header .sp{flex:1}
 main{max-width:960px;margin:1.2rem auto;padding:0 1rem}
 .card{background:#fff;border:1px solid var(--line);border-radius:8px;padding:1rem 1.2rem;margin-bottom:1rem}
 table{width:100%;border-collapse:collapse} th,td{text-align:left;padding:.5rem;border-bottom:1px solid var(--line);vertical-align:top;font-size:.93rem}
 label{display:block;margin:.7rem 0 .2rem;font-weight:600;font-size:.9rem}
 input,select,textarea{width:100%;padding:.5rem;border:1px solid #9aa5b1;border-radius:6px;font:inherit}
 textarea{min-height:110px}
 .btn{background:var(--teal);color:#fff;border:0;border-radius:6px;padding:.5rem .9rem;cursor:pointer;text-decoration:none;display:inline-block;font:inherit}
 .btn.alt{background:#6b7785} .btn.sm{padding:.25rem .6rem;font-size:.85rem}
 .ok{background:#e6f4ea;border:1px solid #9ad0a8;padding:.6rem 1rem;border-radius:6px;margin-bottom:1rem}
 .err{background:#fdecea;border:1px solid #f1a9a0;padding:.6rem 1rem;border-radius:6px;margin-bottom:1rem}
 .aviso{background:#fff6d6;border:1px solid #e0c76a;padding:.6rem 1rem;border-radius:6px;font-size:.88rem;margin-bottom:1rem}
 .tag{display:inline-block;padding:.1rem .5rem;border-radius:10px;font-size:.8rem;background:#e6edf3}
 .tag.Pendiente{background:#fff0c2}.tag.Respondida{background:#d8f0dd}.tag.Vencida{background:#f9d6d2}
 .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:.8rem}
 .kpi{background:#fff;border:1px solid var(--line);border-radius:8px;padding:.8rem}.kpi b{display:block;font-size:1.6rem;color:var(--navy)}
</style>
</head>
<body>
<header>
 <strong>Teleapoyo</strong>
 @auth
  @if(auth()->user()->hasRole('admin'))
   <a href="{{ route('admin.users.index') }}">Usuarios</a><a href="{{ route('admin.catalogs') }}">Especialidades y sedes</a><a href="{{ route('admin.audit') }}">Bitácora</a>
  @elseif(auth()->user()->hasRole('coordinator'))
   <a href="{{ route('indicators.index') }}">Indicadores</a>
  @elseif(auth()->user()->hasRole('specialist'))
   <a href="{{ route('inbox.index') }}">Bandeja</a>
  @else
   <a href="{{ route('consultations.index') }}">Mis consultas</a><a href="{{ route('consultations.create') }}">Nueva consulta</a>
  @endif
  <span class="sp"></span>
  <span>{{ auth()->user()->name }} · {{ auth()->user()->roleLabel() }}</span>
  <form method="post" action="{{ route('logout') }}">@csrf<button class="btn alt sm">Salir</button></form>
 @endauth
</header>
<main>
 @if(session('ok'))<div class="ok">{{ session('ok') }}</div>@endif
 @if($errors->any())<div class="err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
 @yield('content')
</main>
</body>
</html>
