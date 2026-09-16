<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use App\Models\Service;
use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $stats = [
            'total_sent' => EmailLog::where('status', 'sent')->count(),
            'total_failed' => EmailLog::where('status', 'failed')->count(),
            'total_services' => Service::count(),
            'active_keys' => ApiKey::where('is_active', true)->count(),
        ];

        $recentLogs = EmailLog::orderBy('created_at', 'desc')->take(10)->get();

        return view('dashboard', compact('stats', 'recentLogs'));
    }
}
