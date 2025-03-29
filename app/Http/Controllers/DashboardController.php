<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Member;
use App\Models\PaymentHistory;
use App\Enums\OrderStatusEnum;
// use App\Models\User;
// use App\Models\Cashier;
// use App\Models\Item;
// use App\Models\Material;
// use App\Models\Customer;
// use App\Models\Category;

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

            return $this->success('datas retrived successfully',['chart_data' => $chartData, 'total_data' => $totalData]);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching dashboard data'], 500);
        }
    }

    public function memberProfile($id)
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        // Count different statuses directly from the database
        $booked = PaymentHistory::where('member_id', $id)
            ->where('status', OrderStatusEnum::SUCCESS)
            ->count();

        $pending = PaymentHistory::where('member_id', $id)
            ->where('status', OrderStatusEnum::PENDING)
            ->count();

        $reject = PaymentHistory::where('member_id', $id)
            ->where('status', OrderStatusEnum::REJECT)
            ->count();

        // Fetch all payment history for the member
        $paymentHistory = PaymentHistory::where('member_id', $id)->get();

        return response()->json([
            'data' => [
                'member' => $member,
                'payment_history' => $paymentHistory,
                'booked' => $booked,
                'pending' => $pending,
                'reject' => $reject
            ]
        ]);
    }
}
