<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApiLog;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ApiLogController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:api-logs.view', only: ['index']),
        ];
    }
    public function index(Request $request)
    {
        $query = ApiLog::with(['apiKey.user', 'service']);

        if ($search = $request->get('search')) {
            $query->where('endpoint', 'like', "%{$search}%")
                  ->orWhere('ip', 'like', "%{$search}%")
                  ->orWhere('status_code', 'like', "%{$search}%");
        }

        $apiLogs = $query->latest()->paginate(15)->withQueryString();
        $title = 'API Logs & Audit';

        return view('api-logs.index', compact('apiLogs', 'title', 'search'));
    }
}
