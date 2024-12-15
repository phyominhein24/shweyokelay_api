<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index(Request $request)
    {

        DB::beginTransaction();

        try {

            $roles = Role::searchQuery()
                ->sortingQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $roles->transform(function ($role) {
                $role->created_by = $role->created_by ? User::find($role->created_by)->name : "Unknown";
                $role->updated_by = $role->updated_by ? User::find($role->updated_by)->name : "Unknown";
                $role->deleted_by = $role->deleted_by ? User::find($role->deleted_by)->name : "Unknown";
                $role->permissions_count = $role->permissions()->count();
                unset($role->permissions);
                return $role;
            });

            DB::commit();

            return $this->success('roles retrived successfully', $roles);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function store(RoleStoreRequest $request)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        $payload['guard_name'] = 'api';

        try {
            $role = Role::create($payload->toArray());
        
            DB::commit();

            return $this->success('Role created successfully', $role);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();

        try {

            $role = Role::with(['permissions'])->findOrFail($id);
            DB::commit();

            return $this->success('role retrived successfully by id', $role);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function update(RoleUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        $payloadUpdate = [
            'name' => $payload['name'],
            'description' => $payload['description'],
        ];

        try {

            $role = Role::with(['permissions'])->findOrFail($id);
            $role->update($payloadUpdate);
            
            if (isset($payload['permissions'])) {
                $permissionIds = $payload['permissions'];                           
                $permissions = Permission::whereIn('id', $permissionIds)->get();                            
                $role->permissions()->sync($permissions);
            } else {              
                $role->permissions()->detach();
            }
            

            DB::commit();

            return $this->success('role updated successfully by id', $role);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $role = Role::with(['permissions'])->findOrFail($id);

            $getPermissions = $role->toArray()['permissions'];

            $oldPermissions = collect($getPermissions)->map(function ($permission) {
                return $permission['id'];
            });

            $role->revokePermissionTo($oldPermissions);

            $role->delete($id);

            DB::commit();

            return $this->success('role deleted successfully by id', []);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }
}
