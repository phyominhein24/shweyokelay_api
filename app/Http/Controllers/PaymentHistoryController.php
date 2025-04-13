<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentHistoryStoreRequest;
use App\Http\Requests\PaymentHistoryUpdateRequest;
use App\Enums\OrderStatusEnum;
use App\Models\PaymentHistory;
use App\Models\User;
use App\Models\Member;
use App\Models\DailyRoute;
use App\Models\Routes;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PaymentHistoryController extends Controller
{
    public function index(Request $request)
    {
        DB::beginTransaction();
        try {
            $paymentHistorys = PaymentHistory::sortingQuery()
                ->with(['route','member'])
                ->searchQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $paymentHistorys->transform(function ($paymentHistory) {
                $paymentHistory->member_id = $paymentHistory->member_id ? Member::find($paymentHistory->member_id)->name : "Unknown";
                $paymentHistory->route_id = $paymentHistory->route_id ? Routes::find($paymentHistory->route_id)->name : "Unknown";
                $paymentHistory->payment_id = $paymentHistory->payment_id ? Payment::find($paymentHistory->payment_id)->name : "Unknown";

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
            
            if ($request->hasFile('screenshot')) {
                $path = $request->file('screenshot')->store('public/images');
                $image_url = Storage::url($path);
                $payload['screenshot'] = $image_url;
            }
            
            $startDate = Carbon::parse($payload->get('start_time'))->toDateString();

            $dailyRoute = DailyRoute::where('route_id', $payload->get('route_id'))
                ->whereDate('start_date', $startDate)
                ->first();

            if (!$dailyRoute) {
                $dailyRoute = DailyRoute::create([
                    'route_id' => $payload->get('route_id'),
                    'start_date' => $startDate
                ]);
            }

            $payloadArray = $payload->toArray();
            $payloadArray['daily_route_id'] = $dailyRoute->id;

            $paymentHistory = PaymentHistory::create($payloadArray);

            DB::commit();

            return $this->success('paymentHistory created successfully', $paymentHistory);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function store2(PaymentHistoryStoreRequest $request)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        try {
            $paymentHistory = PaymentHistory::create($payload->toArray());
            $paymentHistory->status = OrderStatusEnum::SUCCESS;
            $paymentHistory->save();
            
            if ($request->hasFile('screenshot')) {
                $path = $request->file('screenshot')->store('public/images');
                $image_url = Storage::url($path);
                $payload['screenshot'] = $image_url;
            }
            
            $startDate = Carbon::parse($payload->get('start_time'))->toDateString();

            $dailyRoute = DailyRoute::where('route_id', $payload->get('route_id'))
                ->whereDate('created_at', $startDate)
                ->first();

            if (!$dailyRoute) {
                DailyRoute::create([
                    'route_id' => $payload->get('route_id')
                ]);
            }

            DB::commit();

            return $this->success('paymentHistory created successfully', $paymentHistory);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function confirm($id) 
    {
        DB::beginTransaction();
        try {
            $paymentHistory = PaymentHistory::findOrFail($id);
            $paymentHistory->status = OrderStatusEnum::SUCCESS;
            $paymentHistory->save();

            DB::commit();
            return $this->success('Payment history status updated to SUCCESS', $paymentHistory);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function reject($id) 
    {
        DB::beginTransaction();
        try {
            $paymentHistory = PaymentHistory::findOrFail($id);
            $paymentHistory->status = OrderStatusEnum::REJECT;
            $paymentHistory->save();

            DB::commit();
            return $this->success('Payment history status updated to REJECT', $paymentHistory);
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