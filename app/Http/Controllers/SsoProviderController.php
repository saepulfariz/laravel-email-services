<?php

namespace App\Http\Controllers;

use App\Models\SsoProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SsoProviderController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:sso-providers.view', only: ['index']),
            new Middleware('permission:sso-providers.create', only: ['create', 'store']),
            new Middleware('permission:sso-providers.edit', only: ['edit', 'update']),
            new Middleware('permission:sso-providers.delete', only: ['destroy']),
        ];
    }
    public function index(Request $request)
    {
        $query = SsoProvider::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = ['name', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction === 'asc' ? 'asc' : 'desc');
        }

        $ssoProviders = $query->paginate(10)->withQueryString();
        return view('sso-providers.index', compact('ssoProviders'));
    }

    public function create()
    {
        return view('sso-providers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sso_providers,name',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'can_register' => 'nullable|boolean',
        ]);

        SsoProvider::create($request->all());

        return redirect()->route('sso-providers.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit(SsoProvider $ssoProvider)
    {
        return view('sso-providers.edit', compact('ssoProvider'));
    }

    public function update(Request $request, SsoProvider $ssoProvider)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sso_providers,name,' . $ssoProvider->id,
            'icon' => 'nullable|string|max:255',
            'is_active' => 'nullable',
            'can_register' => 'nullable',
        ]);

        $ssoProvider->is_active = $request->is_active == 'on';
        $ssoProvider->can_register = $request->can_register == 'on';
        $ssoProvider->save();

        return redirect()->route('sso-providers.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy(SsoProvider $ssoProvider)
    {
        $ssoProvider->delete();
        return redirect()->route('sso-providers.index')->with('success', 'Data berhasil dihapus');
    }
}
