<?php

namespace App\Http\Middleware;

use App\Models\Car;
use App\Providers\CarDataProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AddCarTotalCountHeader
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->is('api/cars') && $request->isMethod('GET')) {
            $response->headers->set('X-Total-Count', $request->attributes->get('car_total_count'));
            $response->headers->set('X-Page-Count', $request->attributes->get('_api_operation')->getPaginationItemsPerPage());
        }

        return $response;
    }
}
