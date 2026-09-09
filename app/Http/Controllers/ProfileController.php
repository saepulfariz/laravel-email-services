<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\SsoProvider;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $ssoProviders = SsoProvider::where('is_active', true)->get();
        return view('profile.edit', compact('user', 'ssoProviders'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
