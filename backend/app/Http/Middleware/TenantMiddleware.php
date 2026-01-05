<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

final class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        if (! $user || ! $user->organization_id) {
            abort(403, 'Organization not found');
        }

        // Definir organização atual para RLS
        DB::statement("SET app.current_organization_id = '{$user->organization_id}'");

        $response = $next($request);

        // Limpar após request (apenas PostgreSQL)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('RESET app.current_organization_id');
        }

        return $response;
    }
}
