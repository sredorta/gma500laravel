<?php

namespace App\Http\Middleware;
use JWTAuth;
use Closure;
use App\User;
use Illuminate\Support\Facades\Config;

class Member
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            if ($request->bearerToken() === null) {
                abort(401, 'You must be registered');
            }    
            JWTAuth::setToken($request->bearerToken()) ;
            //Get user id from the payload
            $payload = JWTAuth::parseToken()->getPayload();
            $user = User::find($payload->get('id'));

        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['error'=>'Token is Invalid']);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['error'=>'Token is Expired']);
            }else{
                return response()->json(['error'=>'Something is wrong']);
            }
        }
        if ($user->access != Config::get('constants.ACCESS_MEMBER') && $user->access != Config::get('constants.ACCESS_ADMIN')) {
            abort(401, 'Not granted access for MEMBER');
        }
        //We should here send parameter profile_id to the route so that we don't need to find again
        $request->attributes->add(['isLogged' => true, 'myProfile' => $user->profile_id, 'myAccess' => $user->access]);
        return $next($request);
    }
}
