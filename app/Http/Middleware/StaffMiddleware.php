<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaffMiddleware
{
    use ApiResponser;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role == 'staff' || auth()->user()->role == 'admin') {
            return $next($request);
        }
        return $this->commonResponse('Unauthorized access!', '', Response::HTTP_FORBIDDEN);
    }
}
