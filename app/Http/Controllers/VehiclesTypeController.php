<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehiclesTypeStoreRequest;
use App\Http\Requests\VehiclesTypeUpdateRequest;
use App\Models\Member;
use App\Models\Routes;
use App\Models\VehiclesType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehiclesTypeController extends Controller
{
    public function index(Request $request)
    {
        DB::beginTransaction();
        try {
            $vehiclesTypes = VehiclesType::sortingQuery()
                ->searchQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $vehiclesTypes->transform(function ($vehiclesType) {
                $vehiclesType->member_id = $vehiclesType->member_id ? Member::find($vehiclesType->member_id)->name : "Unknown";
                $vehiclesType->route_id = $vehiclesType->route_id ? Routes::find($vehiclesType->route_id)->name : "Unknown";

                $vehiclesType->created_by = $vehiclesType->created_by ? User::find($vehiclesType->created_by)->name : "Unknown";
                $vehiclesType->updated_by = $vehiclesType->updated_by ? User::find($vehiclesType->updated_by)->name : "Unknown";
                $vehiclesType->deleted_by = $vehiclesType->deleted_by ? User::find($vehiclesType->deleted_by)->name : "Unknown";
                return $vehiclesType;
            });
            DB::commit();
            return $this->success('vehiclesTypes retrived successfully', $vehiclesTypes);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function store(VehiclesTypeStoreRequest $request)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        try {
            $vehiclesType = VehiclesType::create($payload->toArray());
            DB::commit();
            return $this->success('vehiclesType created successfully', $vehiclesType);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();
        try {
            $vehiclesType = VehiclesType::findOrFail($id);
            DB::commit();
            return $this->success('vehiclesType retrived successfully by id', $vehiclesType);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function update(VehiclesTypeUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        try {
            $vehiclesType = VehiclesType::findOrFail($id);
            $vehiclesType->update($payload->toArray());
            DB::commit();
            return $this->success('vehiclesType updated successfully by id', $vehiclesType);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $vehiclesType = VehiclesType::findOrFail($id);
            $vehiclesType->forceDelete();
            DB::commit();
            return $this->success('vehiclesType deleted successfully by id', []);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }
}