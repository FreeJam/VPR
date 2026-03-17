<?php

namespace App\Http\Controllers;

use App\Services\RoleRedirectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, RoleRedirectService $roleRedirectService): RedirectResponse
    {
        return redirect()->route($roleRedirectService->routeNameFor($request->user()));
    }
}
