<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Member;
use App\Models\PaymentHistory;
use App\Enums\OrderStatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Counter;
use Carbon\Carbon;
use App\Models\User;
use Exception;

class DashboardController extends Controller
{
    public function getDashboardData()
    {
        try {

            $chartData = [];

            $shops = Shop::with('itemData.item')->get();

            foreach ($shops as $shop) {
                $shopData = [
                    'name' => $shop->name,
                    'data' => []
                ];

                foreach ($shop->itemData as $itemData) {
                    $shopData['data'][] = [
                        'name' => $itemData->item->name,
                        'count' => $itemData->qty
                    ];
                }

                $chartData[] = $shopData;
            }

            $totalData = [
                // ['name'=> 'Category', 'count' => Category::count()],
                // ['name'=> 'Item', 'count' => Item::count()],
                // ['name'=> 'Material', 'count' => Material::count()],
                // ['name'=> 'Cashier', 'count' => Cashier::count()],
                // ['name'=> 'User', 'count' => User::count()],
                // ['name'=> 'Customer', 'count' => Customer::count()],
                // ['name'=> 'Shop', 'count' => Shop::count()],
            ];

            return $this->success('datas retrived successfully', ['chart_data' => $chartData, 'total_data' => $totalData]);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching dashboard data'], 500);
        }
    }

    public function paymentStats(Request $request)
    {
        $query = PaymentHistory::where('status', OrderStatusEnum::SUCCESS->value);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        $now = Carbon::now();

        $today = (clone $query)->whereDate('created_at', $now->toDateString())->count();
        $week = (clone $query)->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->count();
        $month = (clone $query)->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();
        $year = (clone $query)->whereYear('created_at', $now->year)->count();

        $trend = (clone $query)
            ->whereDate('created_at', '>=', $now->copy()->subDays(6))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('created_at')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->get()
            ->mapWithKeys(fn($item) => [Carbon::parse($item->date)->format('Y-m-d') => $item->count]);

        $fullTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i)->format('Y-m-d');
            $fullTrend[$day] = $trend[$day] ?? 0;
        }

        return response()->json([
            'status' => 200,
            'message' => 'Dashboard data fetched successfully',
            'data' => [
                'today' => $today,
                'week' => $week,
                'month' => $month,
                'year' => $year,
                'trend' => $fullTrend,
            ]
        ], 200);
    }

    public function topAgents()
    {
        $topAgents = DB::table('payment_histories')
            ->join('members', 'payment_histories.member_id', '=', 'members.id')
            ->select(
                'members.id',
                'members.name',
                'members.phone',
                DB::raw('SUM(payment_histories.total) as total_sales')
            )
            ->where('members.is_agent', true)
            ->where('payment_histories.status', OrderStatusEnum::SUCCESS->value)
            ->groupBy('members.id', 'members.name', 'members.phone')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Dashboard data fetched successfully',
            'data' => $topAgents
        ], 200);
    }

    public function memberProfile($id)
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        $booked = PaymentHistory::where('member_id', $id)
            ->where('status', OrderStatusEnum::SUCCESS)
            ->count();

        $pending = PaymentHistory::where('member_id', $id)
            ->where('status', OrderStatusEnum::PENDING)
            ->count();

        $reject = PaymentHistory::where('member_id', $id)
            ->where('status', OrderStatusEnum::REJECT)
            ->count();

        $paymentHistory = PaymentHistory::where('member_id', $id)
            ->with(['route', 'route.vehicles_type'])
            ->get();
        $totalRecords = $paymentHistory->count();

        $paymentHistory->transform(function ($payment) {
            // Check if route exists before accessing it

            if ($payment->route) {
                $startingCounter = Counter::find($payment->route->starting_point);
                $endingCounter = Counter::find($payment->route->ending_point);

                $payment->route->starting_point2 = $startingCounter ? $startingCounter->name : "Unknown";
                $payment->route->ending_point2 = $endingCounter ? $endingCounter->name : "Unknown";
            }

            return $payment;
        });

        return response()->json([
            'data' => [
                'member' => $member,
                'payment_history' => [
                    'records' => $paymentHistory,
                    'totalRecords' => $totalRecords
                ],
                'booked' => $booked,
                'pending' => $pending,
                'reject' => $reject
            ]
        ]);
    }

    public function cancleTicket($id)
    {

        DB::beginTransaction();
        try {
            $paymentHistory = PaymentHistory::findOrFail($id);
            $paymentHistory->status = OrderStatusEnum::CANCLE;
            $paymentHistory->save();

            DB::commit();
            return $this->success('Payment history status updated to CANCLE', $paymentHistory);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function sylCounterProfile($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Sale person not found'], 404);
        }

        $booked = PaymentHistory::where('user_id', $id)
            ->where('status', OrderStatusEnum::SUCCESS)
            ->count();

        $pending = PaymentHistory::where('user_id', $id)
            ->where('status', OrderStatusEnum::PENDING)
            ->count();

        $reject = PaymentHistory::where('user_id', $id)
            ->where('status', OrderStatusEnum::REJECT)
            ->count();

        $paymentHistory = PaymentHistory::where('user_id', $id)
            ->with(['route', 'route.vehicles_type'])
            ->orderBy('created_at', 'DESC')
            ->get();
        $totalRecords = $paymentHistory->count();

        $paymentHistory->transform(function ($payment) {
            // Check if route exists before accessing it

            if ($payment->route) {
                $startingCounter = Counter::find($payment->route->starting_point);
                $endingCounter = Counter::find($payment->route->ending_point);

                $payment->route->starting_point2 = $startingCounter ? $startingCounter->name : "Unknown";
                $payment->route->ending_point2 = $endingCounter ? $endingCounter->name : "Unknown";
            }

            return $payment;
        });
        // dd($paymentHistory);

        return response()->json([
            'data' => [
                'salesperson' => $user,
                'payment_history' => [
                    'records' => $paymentHistory,
                    'totalRecords' => $totalRecords
                ],
                'booked' => $booked,
                'pending' => $pending,
                'reject' => $reject
            ]
        ]);
    }
}
