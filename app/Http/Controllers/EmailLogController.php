<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class EmailLogController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:email-logs.view', only: ['index', 'showBody']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EmailLog::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('to', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        // Sort
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        // Validate sort columns to prevent SQL injection
        $allowedSorts = ['to', 'subject', 'status', 'type', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction === 'asc' ? 'asc' : 'desc');
        }

        $emailLogs = $query->paginate(10)->withQueryString();

        return view('email-logs.index', compact('emailLogs'));
    }

    /**
     * Return the email body for the preview popup.
     */
    public function showBody($id)
    {
        $emailLog = EmailLog::findOrFail($id);

        // Return raw HTML content
        return response($emailLog->body)
            ->header('Content-Type', 'text/html');
    }
}
