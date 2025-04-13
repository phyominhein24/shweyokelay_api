<?php

namespace App\Http\Controllers;

use App\Enums\GeneralStatusEnum;
use App\Http\Requests\WebUserLoginRequest;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebAuthController extends Controller
{
    /**
     * APIs for user login
     *
     * @bodyParam username required.
     * @bodyParam password required.
     */
    public function userProfile(Request $request)
    {
        $email = $request->input('email');

        $user = Member::where('email', $email)->first() 
                ?? User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid email or password'], 404);
        }

        // $user = auth()->guard('member')->user();

        // DB::beginTransaction();
        // $role = Role::with(['permissions'])->findOrFail($user->id);
        // DB::commit();  
        
        // $responseData = [
        //     'user' => [
        //         'id' => $user->id,
        //         'name' => $user->name,
        //         'email' => $user->email,
        //         'phone' => $user->phone,                
        //         'status' => $user->status,                            
        //     ]
        // ];
        return $this->success('user profile retrived successfully', $user);
    }

     public function login(WebUserLoginRequest $request)
     {
         $payload = collect($request->validated());
     
         try {
             $user = Member::where('email', $payload->get('email'))->first() 
                     ?? User::where('email', $payload->get('email'))->first();
     
             if (!$user) {
                 return response()->json(['message' => 'Invalid email or password'], 404);
             }
     
             if ($user->status !== GeneralStatusEnum::ACTIVE->value) {
                 return response()->json(['message' => 'Account is not ACTIVE'], 404);
             }
     
             if (!\Hash::check($payload->get('password'), $user->password)) {
                 return response()->json(['message' => 'Invalid email or password'], 404);
             }
     
             $guard = $user instanceof Member ? 'member' : 'api';
     
             $token = auth()->guard($guard)->login($user);
     
             if (!$token) {
                 \Log::error("JWT Token generation failed for user ID: {$user->id}");
                 return response()->json(['message' => 'Unable to generate token'], 500);
             }
     
             $responseData = [
                 'token' => $token,
                 'user' => [
                     'id' => $user->id,
                     'name' => $user->name,
                     'email' => $user->email,
                     'phone' => $user->phone,
                     'status' => $user->status,
                     'is_admin' => $user instanceof User,
                     'is_agent' => $user->is_agent
                 ],
             ];
             
     
             return $this->success('Login Successfully', $responseData);
     
         } catch (Exception $e) {
             \Log::error("Login error: " . $e->getMessage());
             return response()->json(['message' => 'An error occurred'], 500);
         }
     }
     
    /**
     * APIs for user login out
     */
    public function logout()
    {
        DB::beginTransaction();

        try {
            $user = auth()->guard('api')->user();

            DB::commit();

            if ($user) {
                auth()->guard('api')->logout();

                return response()->json(['message' => 'User successfully signed out'], 400);
                
            }

            return response()->json(['message' => 'Invalid token for logout'], 400);

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * APIs for refresh token
     */

    /**
     * Create new token for user login
     */
    protected function createNewToken($token)
    {
        return $this->success('User successfully signed in', [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user(),
        ]);
    }
}
