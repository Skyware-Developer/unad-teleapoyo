<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Site;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** RF-02: gestión de usuarios y roles (solo administrador). */
class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', ['users' => User::with(['specialty', 'site'])->orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.users.form', $this->formData(new User(['active' => true])));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, null);
        $user = User::create($data);
        AuditLog::record('user_created', 'user', $user->id);

        return redirect()->route('admin.users.index')->with('ok', 'Usuario creado.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', $this->formData($user));
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $user->update($data);
        AuditLog::record('user_updated', 'user', $user->id);

        return redirect()->route('admin.users.index')->with('ok', 'Usuario actualizado.');
    }

    private function formData(User $user): array
    {
        return [
            'user' => $user,
            'specialties' => Specialty::where('active', true)->orderBy('name')->get(),
            'sites' => Site::orderBy('name')->get(),
        ];
    }

    private function validated(Request $request, ?User $user): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:10'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'specialty_id' => ['nullable', 'exists:specialties,id', 'required_if:role,specialist'],
            'site_id' => ['nullable', 'exists:sites,id'],
        ]);
        $data['active'] = $request->boolean('active');
        if ($data['role'] !== 'specialist') {
            $data['specialty_id'] = null;
        }

        return $data;
    }
}
