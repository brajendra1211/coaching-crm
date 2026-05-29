<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.system.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.system.users.form', $this->formData(new User(['role' => 'staff', 'status' => 'active'])));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'User added successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.system.users.form', $this->formData($user));
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validateData($request, $user->id);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function roles()
    {
        $roles = User::query()
            ->selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->orderBy('role')
            ->get();

        return view('admin.system.roles.index', compact('roles'));
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        $emailRule = 'unique:users,email';

        if ($ignoreId) {
            $emailRule .= ',' . $ignoreId;
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $emailRule],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:super_admin,admin,staff,teacher,student,parent'],
            'status' => ['required', 'in:active,inactive'],
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'teacher_id' => ['nullable', 'integer', 'exists:teachers,id'],
            'password' => [$ignoreId ? 'nullable' : 'required', 'string', 'min:6'],
        ]);
    }

    private function formData(User $user): array
    {
        return [
            'user' => $user,
            'students' => Student::orderBy('name')->get(),
            'teachers' => Teacher::orderBy('name')->get(),
        ];
    }
}
