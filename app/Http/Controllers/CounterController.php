<?php

namespace App\Http\Controllers;

use App\Http\Requests\CounterStoreRequest;
use App\Http\Requests\CounterUpdateRequest;
use App\Models\Counter;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CounterController extends Controller
{
    public function index(Request $request)
    {
        DB::beginTransaction();
        try {
            $counters = Counter::sortingQuery()
                ->searchQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $counters->transform(function ($counter) {
                $counter->created_by = $counter->created_by ? User::find($counter->created_by)->name : "Unknown";
                $counter->updated_by = $counter->updated_by ? User::find($counter->updated_by)->name : "Unknown";
                $counter->deleted_by = $counter->deleted_by ? User::find($counter->deleted_by)->name : "Unknown";
                return $counter;
            });
            DB::commit();
            return $this->success('counters retrived successfully', $counters);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function store(CounterStoreRequest $request)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        try {
            $counter = Counter::create($payload->toArray());
            DB::commit();
            return $this->success('counter created successfully', $counter);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();
        try {
            $counter = Counter::findOrFail($id);
            DB::commit();
            return $this->success('counter retrived successfully by id', $counter);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function update(CounterUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        try {
            $counter = Counter::findOrFail($id);
            $counter->update($payload->toArray());
            DB::commit();
            return $this->success('counter updated successfully by id', $counter);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $counter = Counter::findOrFail($id);
            $counter->forceDelete();
            DB::commit();
            return $this->success('counter deleted successfully by id', []);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }
}
