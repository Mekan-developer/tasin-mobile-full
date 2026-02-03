<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_users', only: ['index', 'permissions']),
            new Middleware('permission:edit_users', only: ['assignRole', 'revokeRole', 'syncRoles', 'givePermission']),
        ];
    }

    public function __construct()
    {

    }

    // Получить все роли
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return response()->json($roles);
    }

    // Получить все разрешения
    public function permissions()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    // Назначить роль пользователю
    public function assignRole(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|string|exists:roles,name'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->assignRole($request->role);

        return response()->json([
            'message' => 'Роль успешно назначена',
            'user' => $user->load('roles', 'permissions')
        ]);
    }

    // Удалить роль у пользователя
    public function revokeRole(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|string|exists:roles,name'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->removeRole($request->role);

        return response()->json([
            'message' => 'Роль успешно удалена',
            'user' => $user->load('roles', 'permissions')
        ]);
    }

    // Назначить разрешение пользователю
    public function givePermission(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'permission' => 'required|string|exists:permissions,name'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->givePermissionTo($request->permission);

        return response()->json([
            'message' => 'Разрешение успешно назначено',
            'user' => $user->load('roles', 'permissions')
        ]);
    }

    // Синхронизация ролей пользователя
    public function syncRoles(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'roles' => 'required|array',
            'roles.*' => 'string|exists:roles,name'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->syncRoles($request->roles);

        return response()->json([
            'message' => 'Роли успешно синхронизированы',
            'user' => $user->load('roles', 'permissions')
        ]);
    }
}
