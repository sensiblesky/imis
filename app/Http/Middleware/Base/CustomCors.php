<?php

namespace App\Http\Middleware\Base;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomCors
{
    public function handle(Request $request, Closure $next)
    {
        $origin = $request->headers->get('Origin');
        $allowedOrigin = 'https://imis.weh4ck.wh';

        // Handle preflight requests (OPTIONS)
        if ($request->getMethod() === 'OPTIONS') {
            return response()->json('OK', 200, [
                'Access-Control-Allow-Origin' => $allowedOrigin,
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
                'Access-Control-Max-Age' => '3600',
                'Access-Control-Expose-Headers' => 'Authorization, Content-Type, X-Requested-With',
            ]);
        }

        // For normal requests
        $response = $next($request);

        if ($origin === $allowedOrigin) {
            $response->headers->set('Access-Control-Allow-Origin', $allowedOrigin);
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }

        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        $response->headers->set('Access-Control-Max-Age', '3600');
        $response->headers->set('Access-Control-Expose-Headers', 'Authorization, Content-Type, X-Requested-With');

        return $response;
    }
}