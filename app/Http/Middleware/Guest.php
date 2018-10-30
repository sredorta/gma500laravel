<?php

namespace App\Http\Middleware;
use Symfony\Component\HttpFoundation\Response;
use JWTAuth;
use Closure;
use App\User;

//Checks that user is unregistered

class Guest
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
        $request->attributes->add(['isLogged' => false]);
        if ($request->bearerToken() === null) {
            return $next($request);
        }
        try {
            JWTAuth::setToken($request->bearerToken()) ;
            //Get user id from the payload
            $payload = JWTAuth::parseToken()->getPayload();
            $user = User::find($payload->get('id'));

        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return $next($request);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return $next($request);
            }else{
                
                //return response()->json(['error'=>'Something is wrong']);
            }
        }
        abort(401, 'You cannot be registered');
    }
}
