<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ServiceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:services.view', only: ['index']),
            new Middleware('permission:services.create', only: ['create', 'store']),
            new Middleware('permission:services.edit', only: ['edit', 'update']),
            new Middleware('permission:services.delete', only: ['destroy']),
        ];
    }
    public function index(Request $request)
    {
        $query = Service::query();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');

        if (in_array($sortBy, ['name', 'slug', 'is_active', 'created_at'])) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->latest();
        }

        $services = $query->paginate(10)->withQueryString();
        $title = 'Services Management';

        return view('services.index', compact('services', 'title', 'search'));
    }

    public function create()
    {
        $title = 'Create New Service';
        return view('services.create', compact('title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug',
            'description' => 'nullable|string',
        ]);

        $slug = $request->slug ?: Str::slug($request->name);

        Service::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('services.index')->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        $title = 'Edit Service';
        return view('services.edit', compact('service', 'title'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug,' . $service->id,
            'description' => 'nullable|string',
        ]);

        $slug = $request->slug ?: Str::slug($request->name);

        $service->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Service deleted successfully.');
    }
}
