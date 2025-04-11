<?php

namespace App\Http\Controllers;

use App\Http\Requests\DailyRouteStoreRequest;
use App\Http\Requests\DailyRouteUpdateRequest;
use App\Models\DailyRoute;
use App\Models\VehiclesType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyRouteController extends Controller
{
    public function index(Request $request)
    {
        DB::beginTransaction();
        try {
            $dailyRoutes = DailyRoute::sortingQuery()
                ->with(['route'])
                ->searchQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $dailyRoutes->transform(function ($dailyRoute) {
                $dailyRoute->created_by = $dailyRoute->created_by ? User::find($dailyRoute->created_by)->name : "Unknown";
                $dailyRoute->updated_by = $dailyRoute->updated_by ? User::find($dailyRoute->updated_by)->name : "Unknown";
                $dailyRoute->deleted_by = $dailyRoute->deleted_by ? User::find($dailyRoute->deleted_by)->name : "Unknown";
                return $dailyRoute;
            });
            DB::commit();
            return $this->success('dailyRoutes retrived successfully', $dailyRoutes);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function store(DailyRouteStoreRequest $request)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        try {
            $dailyRoute = DailyRoute::create($payload->toArray());
            DB::commit();
            return $this->success('dailyRoute created successfully', $dailyRoute);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();
        try {
            $dailyRoute = DailyRoute::findOrFail($id);
            DB::commit();
            return $this->success('dailyRoute retrived successfully by id', $dailyRoute);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function update(DailyRouteUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        try {
            $dailyRoute = DailyRoute::findOrFail($id);
            $dailyRoute->update($payload->toArray());
            DB::commit();
            return $this->success('dailyRoute updated successfully by id', $dailyRoute);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $dailyRoute = DailyRoute::findOrFail($id);
            $dailyRoute->forceDelete();
            DB::commit();
            return $this->success('dailyRoute deleted successfully by id', []);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }
}