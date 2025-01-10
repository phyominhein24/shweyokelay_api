<?php

namespace App\Http\Controllers;

use App\Enums\GeneralStatusEnum;
use App\Http\Requests\WebUserLoginRequest;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class WebAuthController extends Controller
{
    /**
     * APIs for user login
     *
     * @bodyParam username required.
     * @bodyParam password required.
     */

     public function login(WebUserLoginRequest $request)
     {
         $payload = collect($request->validated());
     
         try {
            $user = Member::where('email', $payload->get('email'))->first();
        
            if (!$user) {
                return response()->json(['message' => 'Account does not exist'], 404);
            }
        
            if ($user->status !== GeneralStatusEnum::ACTIVE->value) {
                return response()->json(['message' => 'Account is not ACTIVE'], 403);
            }
        
            if (!\Hash::check($payload->get('password'), $user->password)) {
                return response()->json(['message' => 'Invalid email or password'], 401);
            }
        
            // Attempt to generate the token
            $token = auth()->guard('member')->login($user);
        
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
                    'is_agent' => $user->is_agent,
                    'phone' => $user->phone,                
                    'status' => $user->status,                            
                ]
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
    public function refresh()
    {
        DB::beginTransaction();
        try {
            $user = auth()->guard('api')->user();
            DB::commit();

            if ($user) {
                return $this->createNewToken(auth()->guard('api')->refresh());
            }

            return response()->json(['message' => 'Invalid token'], 400);

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

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
