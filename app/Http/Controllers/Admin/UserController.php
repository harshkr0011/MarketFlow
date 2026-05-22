<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount(['assets', 'campaigns'])->get();
        return view('admin.users.index', compact('users'));
    }

    public function impersonate(User $user)
    {
        // Store original admin id in session
        session()->put('impersonate', Auth::id());
        Auth::loginUsingId($user->id);
        
        return redirect()->route('dashboard')->with('status', "You are now impersonating {$user->name}");
    }

    public function leaveImpersonation()
    {
        if (session()->has('impersonate')) {
            $adminId = session()->pull('impersonate');
            Auth::loginUsingId($adminId);
            return redirect()->route('admin.users.index')->with('status', 'Welcome back, Admin.');
        }

        return redirect()->route('dashboard');
    }

    public function upgradeToPro(User $user)
    {
        // Add manual pro subscription record overriding stripe flow
        \Illuminate\Support\Facades\DB::table('subscriptions')->insert([
            'user_id' => $user->id,
            'type' => 'pro',
            'stripe_id' => 'manual_override_' . uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => 'price_pro_trial',
            'quantity' => 1,
            'trial_ends_at' => now()->addDays(14),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('status', "{$user->name} has been upgraded to a 14-day Pro trial.");
    }
}
