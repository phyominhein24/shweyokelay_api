<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactStoreRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Models\Member;
use App\Models\Routes;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        DB::beginTransaction();
        try {
            $contacts = Contact::sortingQuery()
                ->searchQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $contacts->transform(function ($contact) {
                $contact->created_by = $contact->created_by ? User::find($contact->created_by)->name : "Unknown";
                $contact->updated_by = $contact->updated_by ? User::find($contact->updated_by)->name : "Unknown";
                $contact->deleted_by = $contact->deleted_by ? User::find($contact->deleted_by)->name : "Unknown";
                return $contact;
            });
            DB::commit();
            return $this->success('contacts retrived successfully', $contacts);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function store(ContactStoreRequest $request)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        try {
            $contact = Contact::create($payload->toArray());
            DB::commit();
            return $this->success('contact created successfully', $contact);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();
        try {
            $contact = Contact::findOrFail($id);
            DB::commit();
            return $this->success('contact retrived successfully by id', $contact);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function update(ContactUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        try {
            $contact = Contact::findOrFail($id);
            $contact->update($payload->toArray());
            DB::commit();
            return $this->success('contact updated successfully by id', $contact);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $contact = Contact::findOrFail($id);
            $contact->forceDelete();
            DB::commit();
            return $this->success('contact deleted successfully by id', []);
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }
}