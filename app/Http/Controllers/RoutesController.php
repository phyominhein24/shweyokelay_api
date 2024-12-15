<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoutesStoreRequest;
use App\Http\Requests\RoutesUpdateRequest;
use App\Models\Routes;
use App\Models\User;
use App\Models\VehiclesType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoutesController extends Controller
{
    public function index(Request $request)
    {
        DB::beginTransaction();
        try {
            $routess = Routes::sortingQuery()
                ->searchQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $routess->transform(function ($routes) {
                $routes->starting_point = $routes->starting_point ? Routes::find($routes->starting_point)->name : "Unknown";
                $routes->ending_point = $routes->ending_point ? Routes::find($routes->ending_point)->name : "Unknown";
                $routes->vehicles_type_id = $routes->vehicles_type_id ? VehiclesType::find($routes->vehicles_type_id)->name : "Unknown";

                $routes->created_by = $routes->created_by ? User::find($routes->created_by)->name : "Unknown";
                $routes->updated_by = $routes->updated_by ? User::find($routes->updated_by)->name : "Unknown";
                $routes->deleted_by = $routes->deleted_by ? User::find($routes->deleted_by)->name : "Unknown";
                return $routes;
            });
            DB::commit();
            return $this->success('routess retrived successfully', $routess);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function store(RoutesStoreRequest $request)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        try {
            $routes = Routes::create($payload->toArray());
            DB::commit();
            return $this->success('routes created successfully', $routes);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();
        try {
            $routes = Routes::findOrFail($id);
            DB::commit();
            return $this->success('routes retrived successfully by id', $routes);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function update(RoutesUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        try {
            $routes = Routes::findOrFail($id);
            $routes->update($payload->toArray());
            DB::commit();
            return $this->success('routes updated successfully by id', $routes);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $routes = Routes::findOrFail($id);
            $routes->forceDelete();
            DB::commit();
            return $this->success('routes deleted successfully by id', []);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }
}