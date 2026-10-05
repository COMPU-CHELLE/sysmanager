<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordAuditLog
{
    private const array Hidden = ['password', 'password_confirmation', 'current_password', 'username', 'secret'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $user = $request->user();
        $routeName = $request->route()?->getName();

        if ($user === null || $routeName === null || $request->isMethodSafe() || $routeName === 'logout') {
            return $response;
        }

        $parameters = $request->route()->parameters();
        $entityId = collect($parameters)->first(fn (mixed $value): bool => is_numeric($value));
        $companyId = $request->input('company_id');

        AuditLog::create([
            'user_id' => $user->id,
            'company_id' => is_numeric($companyId) ? (int) $companyId : null,
            'action' => $routeName,
            'entity' => explode('.', $routeName)[0],
            'entity_id' => $entityId !== null ? (int) $entityId : null,
            'method' => $request->method(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'payload' => $request->except(self::Hidden),
            'status_code' => $response->getStatusCode(),
        ]);

        return $response;
    }
}
