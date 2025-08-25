<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoutesStoreRequest;
use App\Http\Requests\RoutesUpdateRequest;
use App\Models\Counter;
use App\Models\Routes;
use App\Models\User;
use App\Enums\OrderStatusEnum;
use App\Enums\GeneralStatusEnum;
use App\Models\PaymentHistory;
use App\Models\VehiclesType;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoutesController extends Controller
{
    public function index(Request $request)
    {
        DB::beginTransaction();
        try {
            $isSpecial = $request->input('is_speicial');
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

    // sayar phyo
    public function index_for_web(Request $request)
    {

        DB::beginTransaction();
        try {
            $startingPoint = $request->input('starting_point');
            $endingPoint = $request->input('ending_point');
            $now = Carbon::now(); // full datetime
            $selectedDate = Carbon::parse($request->input('selected_date')); // e.g. 2025-08-24

            $routess = Routes::query()
                ->where('status', GeneralStatusEnum::ACTIVE->value)
                ->when($startingPoint, fn($q, $sp) => $q->where('starting_point', $sp))
                ->when($endingPoint, fn($q, $ep) => $q->where('ending_point', $ep))
                ->when($selectedDate, function ($q, $selectedDate) {
                    $dayOfWeek = $selectedDate->format('l');
                    return $q->whereJsonContains('day_off', $dayOfWeek);
                })
                ->where(function ($q) use ($now, $selectedDate) {
                    $q->whereRaw("
                        ? NOT BETWEEN 
                        datetime(?, departure, '-' || last_min || ' minutes') 
                        AND 
                        datetime(?, departure)
                    ", [
                        $now->format('Y-m-d H:i:s'),
                        $selectedDate->format('Y-m-d'),
                        $selectedDate->format('Y-m-d'),
                    ]);
                })
                ->with(['vehicles_type'])
                ->sortingQuery()
                ->searchQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();
            // ->get();

            $routess->transform(function ($routes) use ($selectedDate) {
                $routes->orders = PaymentHistory::where('route_id', $routes->id)
                    ->where('start_time', $selectedDate)
                    ->whereIn('status', [OrderStatusEnum::PENDING, OrderStatusEnum::SUCCESS])
                    ->get();
                $routes->starting_point2 = $routes->starting_point ? Counter::find($routes->starting_point)->name : "Unknown";
                $routes->ending_point2 = $routes->ending_point ? Counter::find($routes->ending_point)->name : "Unknown";
                $routes->vehicles_type_id = $routes->vehicles_type_id ? VehiclesType::find($routes->vehicles_type_id)->name : "Unknown";

                $routes->created_by = $routes->created_by ? User::find($routes->created_by)->name : "Unknown";
                $routes->updated_by = $routes->updated_by ? User::find($routes->updated_by)->name : "Unknown";
                $routes->deleted_by = $routes->deleted_by ? User::find($routes->deleted_by)->name : "Unknown";

                $departure = Carbon::parse($routes->departure);
                $start = $departure->copy()->subMinutes($routes->last_min)->format('H:i');
                $end   = $departure->format('H:i');

                $routes->between_start = $start;
                $routes->between_end   = $end;
                return $routes;
            });

            DB::commit();

            return $this->success('Routes retrieved successfully', [
                'routes' => $routess,
                'current_time' => $now
            ]);
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e);
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

//  Before fixing last min code:
// public function index_for_web(Request $request)
// {

//     DB::beginTransaction();
//     try {
//         $startingPoint = $request->input('starting_point');
//         $endingPoint = $request->input('ending_point');
//         $selectedDate = $request->input('selected_date');
//         $now = Carbon::now()->format('H:i'); // time only

//         $routess = Routes::query()
//             ->where('status', GeneralStatusEnum::ACTIVE->value)
//             ->when($startingPoint, fn($q, $sp) => $q->where('starting_point', $sp))
//             ->when($endingPoint, fn($q, $ep) => $q->where('ending_point', $ep))
//             ->when($selectedDate, function ($q, $selectedDate) {
//                 $parsedDate = Carbon::parse($selectedDate);
//                 $dayOfWeek = $parsedDate->format('l');
//                 return $q->whereJsonContains('day_off', $dayOfWeek);
//             })
//             ->whereRaw(
//                 "? NOT BETWEEN strftime('%H:%M', departure, '-' || last_min || ' minutes') AND strftime('%H:%M', departure)",
//                 [$now]
//             )
//             ->with(['vehicles_type'])
//             ->sortingQuery()
//             ->searchQuery()
//             ->filterQuery()
//             ->filterDateQuery()
//             ->paginationQuery();
//         // ->get();

//         $routess->transform(function ($routes) use ($selectedDate) {
//             $routes->orders = PaymentHistory::where('route_id', $routes->id)
//                 ->where('start_time', $selectedDate)
//                 ->whereIn('status', [OrderStatusEnum::PENDING, OrderStatusEnum::SUCCESS])
//                 ->get();
//             $routes->starting_point2 = $routes->starting_point ? Counter::find($routes->starting_point)->name : "Unknown";
//             $routes->ending_point2 = $routes->ending_point ? Counter::find($routes->ending_point)->name : "Unknown";
//             $routes->vehicles_type_id = $routes->vehicles_type_id ? VehiclesType::find($routes->vehicles_type_id)->name : "Unknown";

//             $routes->created_by = $routes->created_by ? User::find($routes->created_by)->name : "Unknown";
//             $routes->updated_by = $routes->updated_by ? User::find($routes->updated_by)->name : "Unknown";
//             $routes->deleted_by = $routes->deleted_by ? User::find($routes->deleted_by)->name : "Unknown";

//             $departure = Carbon::parse($routes->departure);
//             $start = $departure->copy()->subMinutes($routes->last_min)->format('H:i');
//             $end   = $departure->format('H:i');

//             $routes->between_start = $start;
//             $routes->between_end   = $end;
//             return $routes;
//         });

//         DB::commit();

//         return $this->success('Routes retrieved successfully', [
//             'routes' => $routess,
//             'current_time' => $now
//         ]);
//     } catch (Exception $e) {
//         DB::rollback();
//         Log::error($e);
//         return $this->internalServerError();
//     }
// }


// my code fixing last min.
// public function index_for_web(Request $request)
// {
//     DB::beginTransaction();
//     try {
//         $startingPoint = $request->input('starting_point');
//         $endingPoint   = $request->input('ending_point');
//         $selectedDate  = $request->input('selected_date');
//         $now           = Carbon::now()->format('H:i'); // current time only

//         $parsedDate = $selectedDate ? Carbon::parse($selectedDate) : null;

//         // Query base routes
//         $routes = Routes::query()
//             ->where('status', GeneralStatusEnum::ACTIVE->value)
//             ->when($startingPoint, fn($q, $sp) => $q->where('starting_point', $sp))
//             ->when($endingPoint, fn($q, $ep) => $q->where('ending_point', $ep))
//             ->when($parsedDate, function ($q, $parsedDate) {
//                 $dayOfWeek = $parsedDate->format('l');
//                 $q->whereJsonContains('day_off', $dayOfWeek);
//             })
//             ->with(['vehicles_type']) // eager load relation
//             ->sortingQuery()
//             ->searchQuery()
//             ->filterQuery()
//             ->filterDateQuery()
//             ->paginationQuery();

//         // Filter per-route cutoff logic (only for today)
//         $routes = $routes->filter(function ($route) use ($parsedDate, $now) {
//             if ($parsedDate && $parsedDate->isToday()) {
//                 $departure = Carbon::parse($route->departure);
//                 $cutoff    = $departure->copy()->subMinutes($route->last_min);

//                 if (Carbon::parse($now)->gt($cutoff)) {
//                     return false; // block expired routes
//                 }
//             }
//             return true;
//         });

//         // Preload data to prevent N+1 queries
//         $counterIds = $routes->pluck('starting_point')
//             ->merge($routes->pluck('ending_point'))
//             ->filter()->unique();
//         $counters = Counter::whereIn('id', $counterIds)->pluck('name', 'id');

//         $vehicleTypeIds = $routes->pluck('vehicles_type_id')->filter()->unique();
//         $vehicleTypes   = VehiclesType::whereIn('id', $vehicleTypeIds)->pluck('name', 'id');

//         $userIds = $routes->pluck('created_by')
//             ->merge($routes->pluck('updated_by'))
//             ->merge($routes->pluck('deleted_by'))
//             ->filter()->unique();
//         $users = User::whereIn('id', $userIds)->pluck('name', 'id');

//         // Transform routes
//         $routes->transform(function ($route) use ($selectedDate, $counters, $vehicleTypes, $users) {
//             $route->orders = PaymentHistory::where('route_id', $route->id)
//                 ->when($selectedDate, fn($q) => $q->where('start_time', $selectedDate))
//                 ->whereIn('status', [OrderStatusEnum::PENDING, OrderStatusEnum::SUCCESS])
//                 ->get();

//             $route->starting_point2 = $route->starting_point ? ($counters[$route->starting_point] ?? "Unknown") : "Unknown";
//             $route->ending_point2   = $route->ending_point ? ($counters[$route->ending_point] ?? "Unknown") : "Unknown";
//             $route->vehicles_type_id = $route->vehicles_type_id ? ($vehicleTypes[$route->vehicles_type_id] ?? "Unknown") : "Unknown";

//             $route->created_by = $route->created_by ? ($users[$route->created_by] ?? "Unknown") : "Unknown";
//             $route->updated_by = $route->updated_by ? ($users[$route->updated_by] ?? "Unknown") : "Unknown";
//             $route->deleted_by = $route->deleted_by ? ($users[$route->deleted_by] ?? "Unknown") : "Unknown";

//             $departure = Carbon::parse($route->departure);
//             $start     = $departure->copy()->subMinutes($route->last_min)->format('H:i');
//             $end       = $departure->format('H:i');

//             $route->between_start = $start;
//             $route->between_end   = $end;

//             return $route;
//         });

//         DB::commit();

//         return $this->success('Routes retrieved successfully', [
//             'routes'        => $routes->values(), // reset indexes
//             'current_time'  => $now
//         ]);
//     } catch (Exception $e) {
//         DB::rollback();
//         Log::error($e);
//         return $this->internalServerError();
//     }
// }