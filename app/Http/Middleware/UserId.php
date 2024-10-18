<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $clientId = $request->header('X-Client-ID');

        if (!$clientId) {
            return response()->json(['error' => 'X-Client-ID header is missing'], 400);
        }
        $user = User::firstOrCreate(
            ['tg_id' => $clientId],
            ['name' => $request->name, 'last_name' => $request->last_name]
        );

        auth()->login($user);

        return $next($request);
    }

}
