<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceItemStoreRequest;
use App\Http\Requests\InvoiceItemUpdateRequest;
use App\Models\InvoiceItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceItemController extends Controller
{
    public function index(Request $request)
    {

        DB::beginTransaction();

        try {

            $orderItem = InvoiceItem::with('user', 'product', 'order')->sortingQuery()
                ->searchQuery()
                ->filterQuery()
                ->paginationQuery();

            DB::commit();

            return $this->success('orderItem retrived successfully', $orderItem);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function store(InvoiceItemStoreRequest $request)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        $payload['ordered_at'] = Carbon::now('Asia/Yangon');
        $payload['user_id'] = Auth::user()->id;

        try {

            $orderItem = InvoiceItem::create($payload->toArray());

            DB::commit();

            return $this->success('orderItem created successfully', $orderItem);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();

        try {

            $orderItem = InvoiceItem::findOrFail($id);
            DB::commit();

            return $this->success('orderItem retrived successfully by id', $orderItem);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function update(InvoiceItemUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        try {

            $orderItem = InvoiceItem::findOrFail($id);
            $orderItem->update($payload->toArray());
            DB::commit();

            return $this->success('orderItem updated successfully by id', $orderItem);

        } catch (Exception $e) {

            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $orderItem = InvoiceItem::findOrFail($id);
            $orderItem->delete($id);

            DB::commit();

            return $this->success('orderItem deleted successfully by id', []);

        } catch (Exception $e) {

            DB::rollback();

            return $this->internalServerError();
        }
    }
}
