<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoutesStoreRequest;
use App\Http\Requests\RoutesUpdateRequest;
use App\Models\Counter;
use App\Models\Routes;
use App\Models\User;
use App\Models\VehiclesType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoutesController extends Controller
{
    public function index(Request $request)
    {
        DB::beginTransaction();
        try {
            $isSpecial = $request->input('is_spicial');
            $startingPoint = $request->input('starting_point');
            $endingPoint = $request->input('ending_point');
            $selectedDate = $request->input('selected_date');
            $now = Carbon::now()->format('Y-m-d H:i:s'); // Format the current time correctly

            $routess = Routes::query()
                ->when($isSpecial, function ($query, $isSpecial) {
                    return $query->whereNotNull('start_date');
                })
                ->when($startingPoint, function ($query, $startingPoint) {
                    return $query->where('starting_point', $startingPoint);
                })
                ->when($endingPoint, function ($query, $endingPoint) {
                    return $query->where('ending_point', $endingPoint);
                })
                ->when($selectedDate, function ($query, $selectedDate) {
                    $parsedDate = Carbon::parse($selectedDate);
                    $dayOfWeek = $parsedDate->format('l');
                    return $query->whereJsonContains('day_off', $dayOfWeek);
                })
                ->whereNotBetween(DB::raw("'$now'"), [
                    DB::raw("datetime(departure, '-' || last_min || ' minutes')"),
                    DB::raw("departure")
                ]) // Exclude when NOW() is between (departure - last_min) and departure
                ->with(['vehicles_type'])
                ->sortingQuery()
                ->searchQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            DB::commit();

            $routess->transform(function ($routes) {
                $routes->starting_point = $routes->starting_point ? Counter::find($routes->starting_point)->name : "Unknown";
                $routes->ending_point = $routes->ending_point ? Counter::find($routes->ending_point)->name : "Unknown";
                $routes->vehicles_type_id = $routes->vehicles_type_id ? VehiclesType::find($routes->vehicles_type_id)->name : "Unknown";

                $routes->created_by = $routes->created_by ? User::find($routes->created_by)->name : "Unknown";
                $routes->updated_by = $routes->updated_by ? User::find($routes->updated_by)->name : "Unknown";
                $routes->deleted_by = $routes->deleted_by ? User::find($routes->deleted_by)->name : "Unknown";
                return $routes;
            });

            DB::commit();
            return $this->success('routess retrieved successfully', $routess);
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