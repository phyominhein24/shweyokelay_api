<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberStoreRequest;
use App\Http\Requests\MemberUpdateRequest;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        DB::beginTransaction();
        try {
            $isAgent = $request->input('is_agent');

            $members = Member::query();

            if ($isAgent !== null) {
                $members->where('is_agent', (bool) $isAgent);
            }
    
            $members = $members->sortingQuery()
                ->searchQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();
    
            $members->transform(function ($member) {
                $member->created_by = $member->created_by ? User::find($member->created_by)->name : "Unknown";
                $member->updated_by = $member->updated_by ? User::find($member->updated_by)->name : "Unknown";
                $member->deleted_by = $member->deleted_by ? User::find($member->deleted_by)->name : "Unknown";
                return $member;
            });
            DB::commit();
            return $this->success('members retrived successfully', $members);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function store(MemberStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $payload = collect($request->validated());

            if ($payload->has('password')) {
                $payload['password'] = bcrypt($payload['password']);
            }

            $member = Member::create($payload->toArray());

            DB::commit();

            return $this->success('Member created successfully', $member);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->internalServerError();
        }
    }


    public function show($id)
    {
        DB::beginTransaction();
        try {
            $member = Member::findOrFail($id);
            DB::commit();
            return $this->success('member retrived successfully by id', $member);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function update(MemberUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        try {
            $member = Member::findOrFail($id);
            $member->update($payload->toArray());
            DB::commit();
            return $this->success('member updated successfully by id', $member);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $member = Member::findOrFail($id);
            $member->forceDelete();
            DB::commit();
            return $this->success('member deleted successfully by id', []);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }
}