<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentHistoryStoreRequest;
use App\Http\Requests\PaymentHistoryUpdateRequest;
use App\Models\PaymentHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentHistoryController extends Controller
{
    public function index(Request $request)
    {
        DB::beginTransaction();
        try {
            $paymentHistorys = PaymentHistory::sortingQuery()
                ->searchQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $paymentHistorys->transform(function ($paymentHistory) {
                $paymentHistory->created_by = $paymentHistory->created_by ? User::find($paymentHistory->created_by)->name : "Unknown";
                $paymentHistory->updated_by = $paymentHistory->updated_by ? User::find($paymentHistory->updated_by)->name : "Unknown";
                $paymentHistory->deleted_by = $paymentHistory->deleted_by ? User::find($paymentHistory->deleted_by)->name : "Unknown";
                return $paymentHistory;
            });
            DB::commit();
            return $this->success('paymentHistorys retrived successfully', $paymentHistorys);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function store(PaymentHistoryStoreRequest $request)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        try {
            $paymentHistory = PaymentHistory::create($payload->toArray());
            DB::commit();
            return $this->success('paymentHistory created successfully', $paymentHistory);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();
        try {
            $paymentHistory = PaymentHistory::findOrFail($id);
            DB::commit();
            return $this->success('paymentHistory retrived successfully by id', $paymentHistory);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function update(PaymentHistoryUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        try {
            $paymentHistory = PaymentHistory::findOrFail($id);
            $paymentHistory->update($payload->toArray());
            DB::commit();
            return $this->success('paymentHistory updated successfully by id', $paymentHistory);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $paymentHistory = PaymentHistory::findOrFail($id);
            $paymentHistory->forceDelete();
            DB::commit();
            return $this->success('paymentHistory deleted successfully by id', []);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }
}