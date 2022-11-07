<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ApiAuthLoginRequest;
use App\Http\Requests\ApiAuthRegisterRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    public function register(ApiAuthRegisterRequest $request){
        try {
            $validated = $request->validated();

            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->password = bcrypt($validated['password']);
            $user->save();

            $token = $user->createToken('token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => 'User created successfully'
            ], Response::HTTP_CREATED);

        } catch (Exception $ex) {
            return response()->json(['error' => $ex], Response::HTTP_BAD_REQUEST);
        }
    }

    public function login(ApiAuthLoginRequest $request){
        try {
            $validated = $request->validated();
            if (!Auth::attempt($validated)) return response()->json([
                'message' => 'Invalid authentication credentials',
            ], Response::HTTP_UNAUTHORIZED);

            $user = User::where('email', $validated['email'])->select('name','email','id')->first();
            $token = $user->createToken('token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => 'Log In successfully'
            ], Response::HTTP_OK);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex], Response::HTTP_BAD_REQUEST);
        }
    }

    public function userProfile(){
        return response()->json([
            'user' => Auth::user(),
            'message' => 'User created successfully'
        ], Response::HTTP_OK);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Log out successfully'], Response::HTTP_OK);
    }
}
