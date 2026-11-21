<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;

/** RF-11: consulta de la bitácora de auditoría. */
class AuditController extends Controller
{
    public function index()
    {
        return view('admin.audit', ['logs' => AuditLog::with('user')->orderByDesc('id')->paginate(50)]);
    }
}
