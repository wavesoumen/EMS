<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('company_id', Auth::user()->company_id)
            ->orderBy('level')
            ->withCount('users')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $availablePermissions = $this->getAvailablePermissions();
        return view('admin.roles.create', compact('availablePermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1|max:100',
            'permissions' => 'nullable|array',
        ]);

        Role::create([
            'name' => $request->name,
            'level' => $request->level,
            'permissions' => $request->permissions ?? [],
            'company_id' => Auth::user()->company_id,
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $availablePermissions = $this->getAvailablePermissions();
        return view('admin.roles.edit', compact('role', 'availablePermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1|max:100',
            'permissions' => 'nullable|array',
        ]);

        $role->update([
            'name' => $request->name,
            'level' => $request->level,
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete a role that has users assigned to it.');
        }

        if ($role->level === 1) {
            return back()->with('error', 'Cannot delete the Admin role.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    private function getAvailablePermissions(): array
    {
        return [
            'manage_company' => 'Manage Company Settings',
            'manage_roles' => 'Manage Roles',
            'manage_users' => 'Manage Users',
            'approve_clockin' => 'Approve Clock-In Requests',
            'view_reports' => 'View Reports',
            'view_all_users' => 'View All Users',
            'manage_attendance' => 'Manage Attendance Records',
        ];
    }
}
