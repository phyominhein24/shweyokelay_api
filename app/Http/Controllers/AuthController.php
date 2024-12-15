<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Enums\GeneralStatusEnum;
use Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AuthRequest $request)
    {
        $payload = collect($request->validated());

        DB::beginTransaction();

        if (! $token = auth()->attempt($payload->toArray())) {
            return response()->json(['message' => 'Incorrect Email or Password '], 403);
        }
        
        $user = Auth::user();

        if ($user->status !== GeneralStatusEnum::ACTIVE->value) {
            return response()->json(['message' => 'Account is not ACTIVE'], 400);
        }

        DB::beginTransaction();
        $role = Role::with(['permissions'])->findOrFail($user->id);
        DB::commit();       
       
        $responseData = [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,                
                'status' => $user->status,                            
            ],
            'role' => $role->name,
            'permissions' => $role->permissions
        ];

        return $this->success('Login Successfully', $responseData);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        $user = auth()->user();

        DB::beginTransaction();
        $role = Role::with(['permissions'])->findOrFail($user->id);
        DB::commit();  
        
        $responseData = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,                
                'status' => $user->status,                            
            ],
            'role' => $role->name,
            'permissions' => $role->permissions
        ];
        return $this->success('user profile retrived successfully', $responseData);
    }

    public function changePassword(ChangePasswordRequest $request, $id)
    {
        $payload = collect($request->validated());
        $payload['password'] = bcrypt($payload['password']);
        $authId = auth()->user()->id;
        if ($authId != $id) {
            return $this->unauthenticated('you do not have permission to change password');
        }

        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);
            $user->update($payload->toArray());
            DB::commit();

            return $this->success('user is successfully change new password', $user);

        } catch (Exception $e) {
            DB::rollback();

            return $e;
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string  $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ]);
    }
}
