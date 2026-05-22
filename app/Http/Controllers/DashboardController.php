<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Mock revenue since we don't have historical invoices in DB
        $totalRevenue = 1245890;
        $revenueGrowth = 24.5;

        // Fetch subscriptions using DB facade to easily query Cashier's table
        $starterCount = \Illuminate\Support\Facades\DB::table('subscriptions')
            ->where('stripe_status', 'active')->where('type', 'starter')->count();
        $proCount = \Illuminate\Support\Facades\DB::table('subscriptions')
            ->where('stripe_status', 'active')->where('type', 'pro')->count();
        $agencyCount = \Illuminate\Support\Facades\DB::table('subscriptions')
            ->where('stripe_status', 'active')->where('type', 'agency')->count();
            
        $totalUsers = $starterCount + $proCount + $agencyCount;

        // Fetch API usage for today
        $todayTokens = \App\Models\ApiUsage::where('service', 'openai')
            ->whereDate('date', now()->toDateString())
            ->sum('tokens_used');
            
        $todayCredits = \App\Models\ApiUsage::where('service', 'midjourney')
            ->whereDate('date', now()->toDateString())
            ->sum('credits_used');

        $assets = \App\Models\Asset::all();

        return view('dashboard', compact(
            'totalRevenue', 'revenueGrowth',
            'starterCount', 'proCount', 'agencyCount', 'totalUsers',
            'todayTokens', 'todayCredits', 'assets'
        ));
    }
}
