<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\TimeBank;
use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('company_id', Auth::user()->company_id)
            ->with(['role', 'supervisor', 'timeBank'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::where('company_id', Auth::user()->company_id)->orderBy('level')->get();
        $supervisors = User::where('company_id', Auth::user()->company_id)
            ->with('role')
            ->orderBy('name')
            ->get();

        return view('admin.users.create', compact('roles', 'supervisors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|min:10|max:15|unique:users,mobile',
            'role_id' => 'required|exists:roles,id',
            'supervisor_id' => 'nullable|exists:users,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'mobile' => preg_replace('/[^0-9]/', '', $request->mobile),
            'role_id' => $request->role_id,
            'company_id' => Auth::user()->company_id,
            'supervisor_id' => $request->supervisor_id,
            'is_active' => true,
        ]);

        TimeBank::create(['user_id' => $user->id, 'total_minutes' => 0]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $editUser = User::with(['role', 'timeBank'])->findOrFail($id);
        $roles = Role::where('company_id', Auth::user()->company_id)->orderBy('level')->get();
        $supervisors = User::where('company_id', Auth::user()->company_id)
            ->where('id', '!=', $id)
            ->with('role')
            ->orderBy('name')
            ->get();

        // Recent attendance
        $recentAttendance = AttendanceRecord::where('user_id', $id)
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        return view('admin.users.edit', compact('editUser', 'roles', 'supervisors', 'recentAttendance'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|min:10|max:15|unique:users,mobile,' . $id,
            'role_id' => 'required|exists:roles,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'is_active' => 'required|boolean',
        ]);

        $user->update([
            'name' => $request->name,
            'mobile' => preg_replace('/[^0-9]/', '', $request->mobile),
            'role_id' => $request->role_id,
            'supervisor_id' => $request->supervisor_id,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
