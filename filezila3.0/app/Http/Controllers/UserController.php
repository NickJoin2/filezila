<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required'
        ]);

        $validator->setCustomMessages([
            'required' => 'field :attribute can not be blank',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 422,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('login', '=', $request->input('login'))->first();

        if (!$user) {
            return response()->json([
                'error' => 401,
                'message' => 'Authentication failed',
            ], 401);
        }


        $password = $user->password;

        if (!($password === $request->password)) {
            return response()->json([
                'error' => 401,
                'message' => 'Authentication failed',
            ], 401);
        }

        $abilities = [];
        if ($user->isAdmin()) $abilities[] = 'admin';
        if ($user->isWaiter()) $abilities[] = 'waiter';
        if ($user->isCook()) $abilities[] = 'cook';

        return response()->json([
            'data' => [
                'user_token' => $user->createToken('token', $abilities)->plainTextToken
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'data' => [
                'message' => 'logout'
            ]
        ]);
    }
}
