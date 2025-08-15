<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Str;

class UserController extends Controller
{

    public function getUserInfo(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'token' => 'required|string',
        ]);

        $token = $request->input('token');

        // API Credentials
        $appid = "kpe474a3a5101c7edb1bf8b84ffadb1b";
        $appkey = '#N$w#%#Goen)qrH8zYM#MARqVtLEsqRc';
        $merch_code = "911004501";
        $version = "1.0";
        $method = "kbz.payment.queryCustInfo";
        $timestamp = time(); // Unix timestamp
        $nonce_str = Str::uuid()->toString();  // Generate a random UUID

        // Create string to sign
        $usertoken = "access_token=" . $token
            . "&appid=" . $appid
            . "&merch_code=" . $merch_code
            . "&method=" . $method
            . "&nonce_str=" . $nonce_str
            . "&resource_type=" . "OPENID"
            . "&timestamp=" . $timestamp
            . "&trade_type=" . "MINIAPP"
            . "&version=" . $version;

        $stringSignuser = $usertoken . "&key=" . $appkey;

        // Create SHA256 signature
        $signtoken = strtoupper(hash('sha256', $stringSignuser));

        // API Request Body
        $userRequest = [
            'data' => [
                'Request' => [
                    'method' => $method,
                    'timestamp' => $timestamp,
                    'nonce_str' => $nonce_str,
                    'version' => $version,
                    'biz_content' => [
                        'merch_code' => $merch_code,
                        'appid' => $appid,
                        'access_token' => $token,
                        'trade_type' => 'MINIAPP',
                        'resource_type' => 'OPENID',
                    ],
                    'sign' => $signtoken,
                    'sign_type' => 'SHA256',
                ]
            ]
        ];

        // uat was removed for production in https://api.kbzpay.com:18443/web/gateway/uat/queryCustInfo
        // Make the HTTP request to the external API
        $response = Http::withOptions([
            'verify' => false, // Disable SSL verification
        ])->post('https://api.kbzpay.com:18443/web/gateway/queryCustInfo', [
            'data' => [
                "Request" => [
                    "method" => "kbz.payment.queryCustInfo",
                    "timestamp" => time(),
                    "nonce_str" => uniqid(),
                    "version" => "1.0",
                    "biz_content" => [
                        "merch_code" => "your_merch_code",
                        "appid" => "your_appid",
                        "access_token" => "your_access_token",
                        "trade_type" => "MINIAPP",
                        "resource_type" => "OPENID"
                    ]
                ]
            ]
        ]);

        // Check if the response is valid and handle accordingly
        if ($response->successful()) {
            $responseData = $response->json();
            $openid = $responseData['body']['Response']['customer_info']['openID'] ?? 'Unknown';
            return response()->json(['openid' => $openid]);
        } else {
            return response()->json(['error' => 'Failed to fetch user info'], 500);
        }
    }

    public function index(Request $request)
    {

        DB::beginTransaction();

        try {

            $users = User::with(['roles'])
                ->searchQuery()
                ->sortingQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $users->transform(function ($user) {
                $user->role_names = $user->roles->isNotEmpty() ? $user->roles[0]->name : null;
                $user->created_by = $user->created_by ? User::find($user->created_by)->name : "Unknown";
                $user->updated_by = $user->updated_by ? User::find($user->updated_by)->name : "Unknown";
                $user->deleted_by = $user->deleted_by ? User::find($user->deleted_by)->name : "Unknown";
                
                return $user;
            });

            DB::commit();

            return $this->success('users retrived successfully', $users);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function store(UserStoreRequest $request)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());
       
        try {

            $user = User::create($payload->toArray());

            if($request->input('role_names')){
                $user->assignRole($request->input('role_names'));
            }

            DB::commit();

            return $this->success('user created successfully', $user);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();

        try {

            $user = User::with(['roles'])->findOrFail($id);
            $user->role_names = $user->roles->isNotEmpty() ? $user->roles[0]->name : null;
            DB::commit();

            return $this->success('user retrived successfully by id', $user);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function update(UserUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        try {

            $user = User::findOrFail($id);

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('public/images');
                $image_url = Storage::url($path);
                $payload['image'] = $image_url;
            }

            // if($request->input('role_names')){
            //     $user->assignRole($request->input('role_names'));
            // }

            if ($request->input('role_names')) {
                $user->syncRoles([$request->input('role_names')]);
            }

            $user->update($payload->toArray());
            $user->role_names = $user->roles->isNotEmpty() ? $user->roles[0]->name : null;

            DB::commit();

            return $this->success('user updated successfully by id', $user);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $user = User::findOrFail($id);
            $user->forceDelete();

            DB::commit();

            return $this->success('user deleted successfully by id', []);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function assignRole(Request $request)
    {
        $payload = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|exists:roles,name',
        ]);

        DB::beginTransaction();

        try {

            $user = User::findOrFail($payload['user_id']);
            $user->assignRole($payload['role']);
            DB::commit();

            return $this->success('role assign successfully', $user);

        } catch (Exception) {
            DB::rollBack();

            return $this->internalServerError();
        }
    }

    public function removeRole(Request $request)
    {
        $payload = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|exists:roles,name',
        ]);

        DB::beginTransaction();

        try {

            $user = User::findOrFail($payload['user_id']);
            $user->removeRole($payload['role']);
            DB::commit();

            return $this->success('role remove successfully', $user);

        } catch (Exception) {
            DB::rollBack();

            return $this->internalServerError();
        }
    }
}
