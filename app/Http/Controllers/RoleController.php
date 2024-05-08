<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Permission;
use App\DataTables\RolesDataTable;
use App\Models\Module;
use App\Models\Plan;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:manage-role|create-role|edit-role|delete-role', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-role', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-role', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-role', ['only' => ['destroy']]);
    }

    public function index(RolesDataTable $dataTable)
    {
        return $dataTable->render('roles.index');
    }

    public function create()
    {
        $permission = Permission::get();
        $view = view('roles.create', compact('permission'));
        return ['html' => $view->render()];
    }

    public function store(Request $request)
    {
        request()->validate([
            'name'              => 'required|string|max:191|unique:roles,name',
        ]);
        $user                   = User::find(\Auth::user()->admin_id);
        $roles                  = Role::where('created_by', \Auth::user()->admin_id)->count();
        $plan                   = Plan::find($user->plan_id);
        if ($roles < $plan->max_roles) {
            Role::create([
                'name'          => $request->input('name'),
                'created_by'    => Auth::user()->admin_id
            ]);
            return redirect()->route('roles.index')
                ->with('success', __('Role created successfully.'));
        } else {
            return redirect()->route('roles.index')->with('failed', __('Your role limit is over, please upgrade plan.'));
        }
    }

    public function show($id)
    {
        $role                   = Role::find($id);
        if ($role->created_by == Auth::user()->admin_id) {
            if ($id == 1) {
                $permissions    = $role->permissions->pluck('name', 'id')->toArray();
                $allPermissions = Permission::all()->pluck('name', 'id')->toArray();
            } else {
                $permissions = DB::table("role_has_permissions")
                    ->select(['role_has_permissions.*', 'permissions.name'])
                    ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where("role_has_permissions.role_id", $id)
                    ->pluck('permissions.name', 'role_has_permissions.permission_id')
                    ->toArray();
                $allPermissions = \Auth::user()->roles->first()->permissions->pluck('name', 'id')->toArray();
            }
            $allModules = Module::all()->pluck('name', 'id')->toArray();
            return view('roles.show')
                ->with('role', $role)
                ->with('permissions', $permissions)
                ->with('allPermissions', $allPermissions)
                ->with('allModules', $allModules);
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        $role           = Role::find($id);
        $view           = View::make('roles.edit', compact('role'));
        return ['html' => $view->render()];
    }

    public function update(Request $request, $id)
    {
        request()->validate([
            'name'              => 'required|string|max:191|unique:roles,name,' . $id,
        ]);
        $role                   = Role::find($id);
        $role->name             = $request->input('name');
        $role->save();
        $role->syncPermissions($request->input('permission'));
        return redirect()->route('roles.index')
            ->with('success', __('Role updated successfully.'));
    }

    public function destroy($id)
    {
        $role                   = Role::find($id);
        $role->delete();
        return redirect()->route('roles.index')
            ->with('success', __('Role deleted successfully.'));
    }

    public function assignPermission(Request $request, $id)
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $role           = Role::find($id);
        $permissions    = $role->permissions()->get();
        $role->revokePermissionTo($permissions);
        $role->givePermissionTo($request->permissions);
        return redirect()->route('roles.index')->with('success',  __('Permissions assigned to role successfully.'));
    }
}
