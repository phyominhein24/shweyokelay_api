<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    public function index(Request $request)
    {

        DB::beginTransaction();

        try {

            $users = User::with(['roles'])
                ->searchQuery()
                ->sortingQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $users->transform(function ($user) {
                $user->role_names = $user->roles->isNotEmpty() ? $user->roles[0]->name : null;
                $user->created_by = $user->created_by ? User::find($user->created_by)->name : "Unknown";
                $user->updated_by = $user->updated_by ? User::find($user->updated_by)->name : "Unknown";
                $user->deleted_by = $user->deleted_by ? User::find($user->deleted_by)->name : "Unknown";
                
                return $user;
            });

            DB::commit();

            return $this->success('users retrived successfully', $users);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function store(UserStoreRequest $request)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());
       
        try {

            $user = User::create($payload->toArray());

            if($request->input('role_names')){
                $user->assignRole($request->input('role_names'));
            }

            DB::commit();

            return $this->success('user created successfully', $user);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();

        try {

            $user = User::with(['roles'])->findOrFail($id);
            $user->role_names = $user->roles->isNotEmpty() ? $user->roles[0]->name : null;
            DB::commit();

            return $this->success('user retrived successfully by id', $user);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function update(UserUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        try {

            $user = User::findOrFail($id);

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('public/images');
                $image_url = Storage::url($path);
                $payload['image'] = $image_url;
            }

            if($request->input('role_names')){
                $user->assignRole($request->input('role_names'));
            }

            $user->update($payload->toArray());
            $user->role_names = $user->roles->isNotEmpty() ? $user->roles[0]->name : null;

            DB::commit();

            return $this->success('user updated successfully by id', $user);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $user = User::findOrFail($id);
            $user->forceDelete();

            DB::commit();

            return $this->success('user deleted successfully by id', []);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function assignRole(Request $request)
    {
        $payload = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|exists:roles,name',
        ]);

        DB::beginTransaction();

        try {

            $user = User::findOrFail($payload['user_id']);
            $user->assignRole($payload['role']);
            DB::commit();

            return $this->success('role assign successfully', $user);

        } catch (Exception) {
            DB::rollBack();

            return $this->internalServerError();
        }
    }

    public function removeRole(Request $request)
    {
        $payload = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|exists:roles,name',
        ]);

        DB::beginTransaction();

        try {

            $user = User::findOrFail($payload['user_id']);
            $user->removeRole($payload['role']);
            DB::commit();

            return $this->success('role remove successfully', $user);

        } catch (Exception) {
            DB::rollBack();

            return $this->internalServerError();
        }
    }
}
