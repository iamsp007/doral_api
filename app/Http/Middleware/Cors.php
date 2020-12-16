<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use Closure;
use Illuminate\Http\Request;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('Access-Control-Allow-Origin')===env('APP_URL')){
            return $next($request)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods',
                    'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers',
                    'Content-Type, Authorization, X-Requested-With, X-XSRF-TOKEN');
        }

        $helper = new Helper();

        return $helper->generateResponse(false,'Forbbiden',null,403);

    }
}
