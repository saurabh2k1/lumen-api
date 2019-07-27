<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\User;

class AuthController extends BaseController
{
    /**
     * post: /login
     * @return string
     */
    public function postLogin(Request $req)
    {
        $credentials = $req->only('email', 'password');

        if ($token = $this->guard()->attempt($credentials)) {
            return $this->respondWithToken($token);
        }

        return response('Unauthorized.', 400);
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout(true);

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        // Refreshed token oken comes as an auth header
        
        return response()->json();
    }

    public function refresh1()
    {
        $startTime = date("Y-m-d H:i:s");
        $adding = '+' . $this->guard()->factory()->getTTL() . ' minutes';
        $token = $this->guard()->refresh();
        // date('Y-m-d H:i:s',strtotime($adding,strtotime($startTime)));
        $expires = $this->guard()->factory()->getTTL();
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires' => $expires,

        ])->header('Authorization', sprintf('Bearer %s', $token));
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $userId = Auth::user()->_id;
        return response()->json([
            // '_id' => $userId,
            'token' => $token,
            'token_type' => 'bearer',
            'expires' => $this->guard()->factory()->getTTL(),
            // 'first_name' => $this->guard()->user()->first_name,
            // 'last_name' => $this->guard()->user()->last_name,
            // 'email' => $this->guard()->user()->email,
            'role' => $this->guard()->user()->role()->value('name'),
            'user' => $this->guard()->user(),
            
        ])->header('Authorization', sprintf('Bearer %s', $token));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }
}
