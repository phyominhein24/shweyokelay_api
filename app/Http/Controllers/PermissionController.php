<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        DB::beginTransaction();

        try {

            $permissions = Permission::get();

            DB::commit();

            return $this->success('permissions are retrived successfully', $permissions);

        } catch (Exception $e) {

            DB::rollBack();
            throw $e;
        }
    }

    public function show($id)
    {
        DB::beginTransaction();

        try {

            $permission = Permission::findOrFail($id);
            DB::commit();

            return $this->success('permission is retrived successfully', $permission);

        } catch (Exception $e) {

            DB::rollBack();
            throw $e;
        }
    }
}
