<?php

namespace App\Http\Middleware;

use App\Models\Car;
use Closure;
use Illuminate\Http\Request;

class AddCarTotalCountHeader
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->is('api/cars') && $request->isMethod('GET')) {
            // ToDo: Its implemented as a quick workaround as long as there are no filtering in api
            $totalCount = Car::count();
            $response->headers->set('X-Total-Count', $totalCount);
            $response->headers->set('X-Page-Count', 20);
        }

        return $response;
    }
}
