<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentHistoryStoreRequest;
use App\Http\Requests\PaymentHistoryUpdateRequest;
use App\Enums\OrderStatusEnum;
use App\Models\PaymentHistory;
use App\Models\User;
use App\Models\Member;
use App\Models\Counter;
use App\Models\DailyRoute;
use App\Models\Routes;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Services\PaymentService;
use App\Utilities\EncryptionHelper;
use App\Utilities\GeneralHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

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

                $$paymentHistory->starting_point2 = $$paymentHistory->starting_point ? Counter::find($$paymentHistory->starting_point)->name : "Unknown";
                $$paymentHistory->ending_point2 = $$paymentHistory->ending_point ? Counter::find($$paymentHistory->ending_point)->name : "Unknown";


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
            
            // $startDate = Carbon::parse($payload->get('start_time'))->toDateString();
            // $dailyRoute = DailyRoute::where('route_id', $payload->get('route_id'))
            //     ->whereDate('start_date', $startDate)
            //     ->first();

            // if (!$dailyRoute) {
            //     $dailyRoute = DailyRoute::create([
            //         'route_id' => $payload->get('route_id'),
            //         'start_date' => $startDate
            //     ]);
            // }
            // $payloadArray = $payload->toArray();
            // $payloadArray['daily_route_id'] = $dailyRoute->id;
            // $payloadArray['status'] = OrderStatusEnum::SUCCESS;
            // $paymentHistory = PaymentHistory::create($payloadArray);
            // // $paymentHistory->status = OrderStatusEnum::SUCCESS;
            // // $paymentHistory->save();
            // DB::commit();

            $totalAmount = $request->input('total_amount');
            $timestamp = (string) GeneralHelper::getUnixTimestamp();
            $nonceStr = GeneralHelper::generateRandomString();
            $orderStr = GeneralHelper::generateRandomString();

            $signParams = [
                'appid' => config('payment.appid'),
                'callback_info' => config('payment.callback_info'),
                'merch_code' => config('payment.merchant_code'),
                'merch_order_id' => $orderStr,
                'method' => config('payment.method'),
                'nonce_str' => $nonceStr,
                'notify_url' => config('payment.notify_url'),
                'timeout_express'=> '100m',
                'timestamp' => $timestamp,
                'title' => 'iPhoneX',
                'total_amount' => $totalAmount,
                'trade_type' => config('payment.trade_type'),
                'trans_currency' => config('payment.trans_currency'),
                'version' => config('payment.version'),
                'key' => config('payment.secret_key')
            ];

            $sign = EncryptionHelper::generateSignature($signParams);
    
            $orderInfo = [
                'Request' => [
                    'timestamp' => $timestamp,
                    'notify_url' => config('payment.notify_url'),
                    'nonce_str' => $nonceStr,
                    'method' => config('payment.method'),
                    'version' => config('payment.version'),
                    'biz_content' => [
                        'merch_order_id' => $orderStr,
                        'merch_code' => config('payment.merchant_code'),
                        'appid' => config('payment.appid'),
                        'trade_type' => config('payment.trade_type'),
                        'title' => 'iPhoneX',
                        'total_amount' => $totalAmount,
                        'trans_currency' => config('payment.trans_currency'),
                        'timeout_express' => config('payment.timeout_express'),
                        'callback_info' => config('payment.callback_info')
                    ],
                    'sign' => $sign,
                    'sign_type' => config('payment.sign_type'),
                ]
            ];

            // dd($orderInfo['Request']);
    
            // $response = Http::post('http://api.kbzpay.com/payment/gateway/uat/precreate', $orderInfo); // for uat
            $response = Http::post('http://api.kbzpay.com/payment/gateway/precreate', $orderInfo); // for production

            $signParams2 = [
                'appid' => config('payment.appid'),
                'merch_code' => config('payment.merchant_code'),
                'nonce_str' => $nonceStr,
                'prepay_id' => $response['Response']['prepay_id'] ?? null,
                'timestamp' => $timestamp,
                'key' => config('payment.secret_key')
            ];

            $sign2 = EncryptionHelper::getSignForOrderInfo($signParams2);
            $sign2String = EncryptionHelper::getSignForOrderInfoString($signParams2);

            // Log::info('POST Request to: http://api.kbzpay.com/payment/gateway/uat/precreate?' . http_build_query($orderInfo['Request']));
            
            // dd($response);
            return response()->json([
                'status' => 200,
                'message' => 'successfully',
                'data' => [
                    // 'response' => $response['Response'] ?? null,
                    // 'request' => $orderInfo['Request'],
                    // 'signParams' => $signParams,
                    'result' => $response['Response']['result'] ?? null,
                    'prepay_id' => $response['Response']['prepay_id'] ?? null,
                    'orderInfo' => $sign2String,
                    'sign' => $sign2,
                    'signType' => $response['Response']['sign_type'] ?? null
                ]
            ], 200);
            

            // return $this->success('paymentHistory created successfully', $paymentHistory);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function store3(PaymentHistoryStoreRequest $request)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        try {
            
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
            $payloadArray['status'] = OrderStatusEnum::SUCCESS;
            $paymentHistory = PaymentHistory::create($payloadArray);
            // $paymentHistory->status = OrderStatusEnum::SUCCESS;
            // $paymentHistory->save();
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
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return $this->notFound('Payment history not found.');
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
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return $this->notFound('Payment history not found.');
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function authGetUserInfo(Request $request)
    {
        $access_token = $request->input('access_token');
        $timestamp = (string) GeneralHelper::getUnixTimestamp();
        $nonceStr = GeneralHelper::generateRandomString();

        $signParams = [
            'access_token' => $access_token,
            'appid' => config('payment.appid'),
            'merch_code' => config('payment.merchant_code'),
            'method' => config('payment.method'),
            'nonce_str' => $nonceStr,
            'resource_type' => 'OPENID',
            'timestamp' => $timestamp,
            'trade_type' => config('payment.trade_type'),
            'version' => config('payment.version'),
            'key' => config('payment.secret_key')
        ];

        $sign = EncryptionHelper::getSignForOrderInfo2($signParams);

        $orderInfo = [
            'Request' => [
                'method' => config('payment.method'),
                'timestamp' => $timestamp,
                'nonce_str' => $nonceStr,
                'version' => config('payment.version'),
                'biz_content' => [
                    'merch_code' => config('payment.merchant_code'),
                    'appid' => config('payment.appid'),
                    'access_token' => $access_token,
                    'trade_type' => config('payment.trade_type'),
                    'resource_type'=> "OPENID",
                ],
                'sign' => $sign,
                'sign_type' => config('payment.sign_type'),
            ]
        ];
        
        // $response = Http::post('https://api.kbzpay.com:18443/web/gateway/uat/queryCustInfo', $orderInfo);
        $response = Http::post('https://api.kbzpay.com:18443/web/gateway/queryCustInfo', $orderInfo); // for production
        $responseData = $response->json();
        $openID = $responseData['Response']['customer_info']['openID'] ?? null;
    
        return response()->json([
            'status' => 200,
            'message' => 'Successfully',
            'data' => [
                'orderInfo' => $orderInfo,
                'response' => $responseData,
                'openID' => $openID
            ]
        ], 200);
        
    }

    public function showKpayMemberTicket($id)
    {
        DB::beginTransaction();
        try {
            $paymentHistories = PaymentHistory::where('kpay_member_id', $id)->with(['route.vehicles_type'])->get();

            $paymentHistories->transform(function ($paymentHistory) {
                
                $paymentHistory->starting_point = $paymentHistory->route->starting_point ? Counter::find($paymentHistory->route->starting_point)->name : "Unknown";
                $paymentHistory->ending_point = $paymentHistory->route->ending_point ? Counter::find($paymentHistory->route->ending_point)->name : "Unknown";

                return $paymentHistory;
            });

            DB::commit();
            return $this->success('Payment histories retrieved successfully', $paymentHistories);
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