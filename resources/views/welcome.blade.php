<?php
use App\Models\Campaign;
use App\Models\Asset;
use App\Models\Product;
use App\Models\Post;
use App\Models\AdSpendLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Query dynamic stats from the database
$campaignsCount = Campaign::count() ?: 12;
$activeCampaignsCount = Campaign::where('status', 'Active')->count() ?: 8;

// Subscriber / Customer counts
$subscribersCount = DB::table('subscriptions')->count() ?: 45;
$totalRevenue = 1245000 + ($subscribersCount * 2499);

// Ad Spend calculation
$totalAdSpend = AdSpendLog::sum('spend_amount') ?: 210000;

// Average stats based on Campaign logs if available
$campaigns = Campaign::all();
$averageCpl = 45.50;
$averageCtr = 8.2;

if ($campaigns->isNotEmpty()) {
    $cplSum = 0;
    $ctrSum = 0;
    $validCount = 0;
    foreach ($campaigns as $c) {
        $cpl = $c->cpl ?? ($c->cost_per_lead ?? null);
        $ctr = $c->ctr ?? ($c->click_through_rate ?? null);
        if ($cpl !== null) {
            $cplSum += $cpl;
            $ctrSum += $ctr ?? 0;
            $validCount++;
        }
    }
    if ($validCount > 0) {
        $averageCpl = $cplSum / $validCount;
        $averageCtr = $ctrSum / $validCount;
    }
}

// Fetch latest dynamic activities
$latestCampaigns = Campaign::latest()->take(3)->get();
$allAssets = Asset::all();

$liveActivities = [];
if ($latestCampaigns->isNotEmpty()) {
    foreach ($latestCampaigns as $c) {
        $timeAgo = $c->updated_at ? $c->updated_at->diffForHumans() : 'Just now';
        $statusColor = $c->status == 'Active' ? 'text-emerald-400' : 'text-yellow-400';
        $liveActivities[] = [
            'type' => 'campaign',
            'title' => "Campaign '{$c->name}' is now <span class='font-bold {$statusColor}'>{$c->status}</span>",
            'time' => $timeAgo,
            'icon' => '<svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>',
            'bg' => 'bg-emerald-500/20 border-emerald-500/30'
        ];
    }
}

// Fetch latest spend logs
try {
    $latestAdSpend = AdSpendLog::with('campaign')->latest()->take(2)->get();
    foreach ($latestAdSpend as $spend) {
        $timeAgo = $spend->recorded_date ? \Carbon\Carbon::parse($spend->recorded_date)->diffForHumans() : ($spend->created_at ? $spend->created_at->diffForHumans() : 'Recently');
        $campaignName = $spend->campaign->name ?? 'Global';
        $liveActivities[] = [
            'type' => 'spend',
            'title' => "Recorded ad spend of <span class='font-bold text-neon-cyan'>₹" . number_format($spend->spend_amount) . "</span> on " . ucfirst($spend->platform) . " for '{$campaignName}'",
            'time' => $timeAgo,
            'icon' => '<svg class="w-4 h-4 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>',
            'bg' => 'bg-pink-500/20 border-pink-500/30'
        ];
    }
} catch (\Exception $e) {
    // Silent catch
}

if (count($liveActivities) < 3) {
    $liveActivities[] = [
        'type' => 'mock',
        'title' => "New lead from <span class='font-bold text-neon-purple'>Mumbai, MH</span> via Instagram Ads",
        'time' => '2 mins ago',
        'icon' => '<svg class="w-4 h-4 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>',
        'bg' => 'bg-pink-500/20 border-pink-500/30'
    ];
    $liveActivities[] = [
        'type' => 'mock',
        'title' => "Campaign 'Diwali Spark' ROI hit <span class='font-bold text-emerald-400'>5.2x</span>",
        'time' => '15 mins ago',
        'icon' => '<svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>',
        'bg' => 'bg-emerald-500/20 border-emerald-500/30'
    ];
    $liveActivities[] = [
        'type' => 'mock',
        'title' => "WhatsApp Automation sent to <span class='font-bold'>1,200 contacts</span> in Bengaluru",
        'time' => '1 hour ago',
        'icon' => '<svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>',
        'bg' => 'bg-green-500/20 border-green-500/30'
    ];
}

// Map assets JSON
if ($allAssets->isEmpty()) {
    $fallbackAssets = [
        [
            'id' => 1,
            'title' => 'Diwali Mega Sale Kit',
            'category' => 'Social Media',
            'type' => 'Image',
            'file_path' => '#',
            'thumbnail_path' => 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?q=80&w=1000&auto=format&fit=crop',
            'price_tier' => 'Free',
            'extension' => 'PSD',
            'description' => '12 Instagram Post Templates optimized for festive sales.'
        ],
        [
            'id' => 2,
            'title' => 'B2B SaaS Playbook 2026',
            'category' => 'Strategy Playbook',
            'type' => 'File',
            'file_path' => '#',
            'thumbnail_path' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=1000&auto=format&fit=crop',
            'price_tier' => 'Pro',
            'extension' => 'PDF',
            'description' => 'The ultimate guide to generating high-quality B2B leads.'
        ],
        [
            'id' => 3,
            'title' => 'Cart Abandonment Sequence',
            'category' => 'Email Blueprint',
            'type' => 'File',
            'file_path' => '#',
            'thumbnail_path' => 'https://images.unsplash.com/photo-1596526131083-e8c633c948d2?q=80&w=1000&auto=format&fit=crop',
            'price_tier' => 'Free',
            'extension' => 'HTML',
            'description' => 'A 3-part email flow designed to recover lost e-commerce sales.'
        ],
        [
            'id' => 4,
            'title' => 'Real Estate Meta Ads',
            'category' => 'Ad Creative',
            'type' => 'Image',
            'file_path' => '#',
            'thumbnail_path' => 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?q=80&w=1000&auto=format&fit=crop',
            'price_tier' => 'Pro',
            'extension' => 'FIG',
            'description' => 'High-conversion Figma templates for property listings.'
        ]
    ];
    $assetsJson = json_encode($fallbackAssets);
} else {
    $assetsJson = $allAssets->map(function ($asset) {
        $cat = $asset->category;
        if ($cat === 'Ad Creatives') $cat = 'Ad Creative';
        if ($cat === 'Email Blueprints') $cat = 'Email Blueprint';
        if ($cat === 'Playbooks') $cat = 'Strategy Playbook';
        
        $ext = 'PSD';
        if ($asset->type === 'PDF' || stripos($asset->file_path, '.pdf') !== false) {
            $ext = 'PDF';
        } elseif ($asset->type === 'HTML' || stripos($asset->file_path, '.html') !== false) {
            $ext = 'HTML';
        } elseif ($asset->type === 'Figma' || stripos($asset->file_path, '.fig') !== false) {
            $ext = 'FIG';
        }

        return [
            'id' => $asset->id,
            'title' => $asset->title,
            'category' => $cat ?: 'Social Media',
            'type' => $asset->type ?: 'Image',
            'file_path' => $asset->file_path ?: '#',
            'thumbnail_path' => $asset->thumbnail_path ?: ($asset->file_path ?: 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?q=80&w=1000&auto=format&fit=crop'),
            'price_tier' => $asset->price_tier ?: 'Free',
            'extension' => $ext,
            'description' => $asset->description ?: ($cat . ' template optimized for performance.'),
        ];
    })->toJson();
}
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>MarketFlow - E-Portal for Indian Brands & Agencies</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-navy-900 selection:bg-neon-purple selection:text-white">
        
        <!-- Background Container -->
        <div class="relative overflow-hidden bg-slate-900 min-h-screen flex flex-col justify-between pb-12">
            
            <!-- Navigation Header -->
            <header class="w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center relative z-50">
                <div class="flex items-center space-x-2">
                    <div class="h-8 w-8 rounded bg-gradient-to-tr from-neon-purple to-neon-cyan flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <span class="text-xl font-bold text-white font-outfit tracking-wider">Market<span class="text-neon-cyan">Flow</span></span>
                </div>
                @if (Route::has('login'))
                    <livewire:welcome.navigation />
                @endif
            </header>
            
            <!-- Animated Blobs (The Movement) -->
            <div class="absolute top-1/4 -left-4 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute top-1/3 -right-4 w-72 h-72 bg-cyan-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-1/3 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>

            <!-- The Glass Card (The Hero Content) -->
            <div class="relative z-10 flex-1 flex items-center justify-center px-4 py-8">
                <div class="backdrop-blur-xl bg-white/5 border border-white/10 p-6 sm:p-12 rounded-3xl shadow-2xl max-w-4xl text-center transition-all duration-300 hover:border-white/20 hover:bg-white/10 w-full">
                    <h1 class="text-3xl sm:text-5xl md:text-6xl font-extrabold text-white tracking-tight font-outfit">
                        E-Portal for <span class="bg-clip-text text-transparent bg-gradient-to-r from-neon-cyan to-neon-purple">Indian Brands & Agencies</span>
                    </h1>
                    <p class="mt-6 text-sm sm:text-lg text-slate-300 font-inter">
                        Scale your business with automated WhatsApp funnels, Hinglish AI copywriting, and real-time INR analytics.
                    </p>
                    <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-8 py-3 bg-gradient-to-r from-neon-cyan to-blue-600 text-white rounded-full font-bold hover:scale-105 transition-transform duration-300 shadow-lg shadow-neon-cyan/20">
                            Go to Workspace
                        </a>
                        <a href="{{ url('/admin') }}" class="px-8 py-3 bg-gradient-to-r from-neon-purple to-indigo-600 text-white rounded-full font-bold hover:scale-105 transition-transform duration-300 shadow-lg shadow-neon-purple/20">
                            Admin Panel
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="px-8 py-3 bg-gradient-to-r from-neon-cyan to-blue-600 text-white rounded-full font-bold hover:scale-105 transition-transform duration-300 shadow-lg shadow-neon-cyan/20">
                            Start Project
                        </a>
                        <a href="{{ route('login') }}" class="px-8 py-3 backdrop-blur-md bg-white/10 text-white border border-white/20 hover:bg-white/20 rounded-full font-bold transition-all duration-300">
                            Sign In
                        </a>
                    @endauth
                </div>
            </div>
            
        </div>


        <!-- Bento Grid Ecosystem Section -->
        <section class="py-20 bg-slate-900 px-6">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-4xl font-bold text-white mb-12 text-center font-outfit">Our Marketing Ecosystem</h2>
                
                <!-- Bento Grid Container -->
                <div class="grid grid-cols-1 md:grid-cols-4 md:grid-rows-2 gap-6">
                    
                    <!-- Large Feature Card (2x2) -->
                    <div class="md:col-span-2 md:row-span-2 bg-white/5 border border-white/10 rounded-[2rem] p-8 hover:bg-white/10 hover:shadow-[0_0_30px_rgba(34,197,94,0.15)] hover:border-green-400/30 transition-all duration-300 group overflow-hidden relative">
                        <div class="relative z-10">
                            <span class="text-cyan-400 font-mono text-sm uppercase tracking-widest">Master Tool</span>
                            <h3 class="text-3xl font-bold text-white mt-4 font-outfit group-hover:text-cyan-300 transition-colors">WhatsApp & AI Campaign Engine</h3>
                            <p class="text-slate-400 mt-4 max-w-xs font-inter">Automate your entire marketing workflow with WhatsApp broadcasts and localized AI funnels tailored for Indian D2C & SaaS.</p>
                        </div>
                        <!-- Interactive Graphic in background -->
                        <div class="absolute bottom-0 right-0 w-1/2 h-1/2 bg-gradient-to-br from-cyan-500/20 to-transparent blur-2xl group-hover:scale-110 transition-transform duration-700"></div>
                        <div class="absolute -bottom-10 -right-10 opacity-50 group-hover:opacity-100 transition-opacity duration-500">
                            <svg class="w-48 h-48 text-cyan-500/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        </div>
                    </div>

                    <!-- Horizontal Card (2x1) -->
                    <div class="md:col-span-2 bg-white/5 border border-white/10 rounded-[2rem] p-8 flex flex-col sm:flex-row items-center justify-between hover:bg-white/10 hover:-translate-y-1 hover:shadow-neon-purple/10 transition-all duration-300 group">
                        <div class="mb-6 sm:mb-0">
                            <h3 class="text-xl font-bold text-white font-outfit">Advanced Analytics (INR)</h3>
                            <p class="text-slate-400 font-inter">Real-time revenue & ROI tracking in Rupees.</p>
                        </div>
                        <div class="h-16 w-24 bg-slate-800 rounded-lg border border-white/5 flex items-end p-2 gap-1 overflow-hidden">
                            <div class="w-full bg-cyan-500 h-[30%] group-hover:h-[60%] transition-all duration-500"></div>
                            <div class="w-full bg-purple-500 h-[60%] group-hover:h-[80%] transition-all duration-500 delay-75"></div>
                            <div class="w-full bg-blue-500 h-[40%] group-hover:h-[100%] transition-all duration-500 delay-150"></div>
                        </div>
                    </div>

                    <!-- Square Card (1x1) -->
                    <div class="bg-white/5 border border-white/10 rounded-[2rem] p-8 hover:bg-white/10 hover:shadow-[0_0_20px_rgba(236,72,153,0.2)] hover:border-pink-500/30 transition-all duration-300 group text-center flex flex-col items-center justify-center">
                        <div class="text-4xl mb-4 group-hover:-translate-y-2 transition-transform duration-300">🚀</div>
                        <h3 class="text-lg font-bold text-white font-outfit">D2C Growth</h3>
                    </div>

                    <!-- Square Card (1x1) -->
                    <div class="bg-white/5 border border-white/10 rounded-[2rem] p-8 hover:bg-white/10 hover:shadow-[0_0_20px_rgba(168,85,247,0.2)] hover:border-purple-500/30 transition-all duration-300 group text-center flex flex-col items-center justify-center">
                        <div class="text-4xl mb-4 group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300">✍️</div>
                        <h3 class="text-lg font-bold text-white font-outfit">Hinglish AI Copy</h3>
                    </div>

                </div>
            </div>
        </section>

        <!-- Analytics Dashboard (India-SaaS localized) -->
        <section class="py-20 bg-slate-900 px-6">
            <div class="max-w-7xl mx-auto">
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Left Column: The Dashboard -->
                    <div class="lg:col-span-2 bg-slate-900 border border-white/10 rounded-2xl p-6 shadow-2xl relative overflow-hidden group">
                        <!-- Header with Currency Toggle -->
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-8 gap-4">
                            <div>
                                <h3 class="text-2xl font-bold text-white font-outfit">Campaign Overview</h3>
                                <p class="text-sm text-slate-400 font-inter mt-1">Real-time performance metrics across your funnels.</p>
                            </div>
                            <span class="px-3 py-1 bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 rounded-full text-xs font-mono flex items-center w-max">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Currency: INR (₹)
                            </span>
                        </div>

                        <!-- KPI Grid -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                            <div class="p-4 bg-white/5 hover:bg-white/10 transition-colors rounded-xl border border-white/5 hover:border-white/10">
                                <p class="text-slate-400 text-xs font-inter uppercase tracking-wider">Total Revenue</p>
                                <p class="text-xl sm:text-2xl font-bold text-emerald-400 mt-2 font-outfit">₹{{ number_format($totalRevenue) }}</p>
                            </div>
                            <div class="p-4 bg-white/5 hover:bg-white/10 transition-colors rounded-xl border border-white/5 hover:border-white/10">
                                <p class="text-slate-400 text-xs font-inter uppercase tracking-wider">Total Ad Spend</p>
                                <p class="text-xl sm:text-2xl font-bold text-white mt-2 font-outfit counter-up" data-target="{{ $totalAdSpend }}">₹0</p>
                            </div>
                            <div class="p-4 bg-white/5 hover:bg-white/10 transition-colors rounded-xl border border-white/5 hover:border-white/10">
                                <p class="text-slate-400 text-xs font-inter uppercase tracking-wider">Avg CPL</p>
                                <p class="text-xl sm:text-2xl font-bold text-white mt-2 font-outfit">₹{{ number_format($averageCpl, 2) }}</p>
                            </div>
                            <div class="p-4 bg-white/5 hover:bg-white/10 transition-colors rounded-xl border border-white/5 hover:border-white/10">
                                <p class="text-slate-400 text-xs font-inter uppercase tracking-wider">Avg CTR</p>
                                <p class="text-xl sm:text-2xl font-bold text-neon-cyan mt-2 font-outfit">{{ number_format($averageCtr, 1) }}%</p>
                            </div>
                        </div>

                        <!-- The Main Chart (Using ApexCharts) -->
                        <div id="analytics-chart" class="h-64 w-full"></div>
                        
                        <!-- Decorative glow -->
                        <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-500/20 rounded-full mix-blend-screen filter blur-3xl opacity-50 group-hover:opacity-100 transition-opacity duration-700"></div>
                    </div>

                    <!-- Right Column: Regional Activity Feed -->
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-2xl flex flex-col">
                        <h3 class="text-lg font-bold text-white mb-6 font-outfit border-b border-white/10 pb-4">Live Activity</h3>
                        <div class="flex-1 space-y-6 overflow-y-auto pr-2 custom-scrollbar" style="max-height: 22rem;">
                            @foreach($liveActivities as $activity)
                                <div class="flex gap-4">
                                    <div class="mt-1 flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center border {{ $activity['bg'] }}">
                                            {!! $activity['icon'] !!}
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-white font-inter">{!! $activity['title'] !!}</p>
                                        <p class="text-xs text-slate-500 mt-1">{{ $activity['time'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <style>
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); border-radius: 4px; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 4px; }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Number Formatting for INR
                const formatINR = (number) => {
                    return new Intl.NumberFormat('en-IN', {
                        style: 'currency',
                        currency: 'INR',
                        maximumFractionDigits: 0
                    }).format(number);
                };

                // Initialize ApexCharts
                var options = {
                    chart: { 
                        type: 'area', 
                        height: 280, 
                        toolbar: { show: false }, 
                        animations: { enabled: true, easing: 'linear', dynamicAnimation: { speed: 1000 } },
                        fontFamily: 'Inter, sans-serif',
                        background: 'transparent'
                    },
                    colors: ['#6366f1'], // Indigo
                    series: [{ name: 'Revenue', data: [31000, 40000, 28000, 51000, 42000, 109000, 120000] }],
                    xaxis: { 
                        categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], 
                        labels: { style: { colors: '#94a3b8' } },
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        tooltip: { enabled: false }
                    },
                    yaxis: { 
                        labels: { 
                            formatter: (val) => formatINR(val), 
                            style: { colors: '#94a3b8' } 
                        } 
                    },
                    grid: {
                        borderColor: 'rgba(255,255,255,0.05)',
                        strokeDashArray: 4,
                    },
                    stroke: { curve: 'smooth', width: 2 },
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] } },
                    dataLabels: { enabled: false },
                    tooltip: {
                        theme: 'dark',
                        y: { formatter: function (val) { return formatINR(val) } }
                    }
                };
                
                if(document.querySelector("#analytics-chart")) {
                    var chart = new ApexCharts(document.querySelector("#analytics-chart"), options);
                    chart.render();
                }

                // Counter Animation logic (Triggered on intersection)
                const counters = document.querySelectorAll('.counter-up');
                const observerOptions = { root: null, rootMargin: '0px', threshold: 0.5 };
                
                const observer = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const target = entry.target;
                            const targetValue = parseInt(target.getAttribute('data-target'));
                            let startValue = 0;
                            const duration = 2000; 
                            const stepTime = Math.abs(Math.floor(duration / 50));
                            
                            const timer = setInterval(() => {
                                startValue += (targetValue / 50);
                                if (startValue >= targetValue) {
                                    startValue = targetValue;
                                    clearInterval(timer);
                                }
                                target.textContent = formatINR(startValue);
                            }, stepTime);
                            
                            observer.unobserve(target);
                        }
                    });
                }, observerOptions);

                counters.forEach(counter => observer.observe(counter));
            });
           <!-- Global Marketing Asset Library -->
        <section class="py-20 bg-slate-950 border-t border-white/5 relative" x-data="{ activeCategory: 'All', searchQuery: '', assets: {{ $assetsJson }} }">
            <div class="max-w-7xl mx-auto px-6">
                <!-- Search & Filter Header -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-12">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-bold text-white font-outfit">Marketing <span class="text-cyan-400">Assets</span></h2>
                        <p class="text-slate-400 font-inter mt-2">The digital vault for all your templates and resources.</p>
                    </div>
                    <div class="relative w-full md:w-96 group">
                        <div class="absolute -inset-0.5 bg-gradient-to-r from-cyan-500 to-purple-500 rounded-full blur opacity-20 group-hover:opacity-40 transition duration-500"></div>
                        <div class="relative flex items-center bg-slate-900 border border-white/10 rounded-full overflow-hidden">
                            <span class="pl-5 text-slate-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input type="text" x-model="searchQuery" class="w-full bg-transparent border-none py-3 px-4 text-slate-300 font-inter focus:ring-0 outline-none placeholder:text-slate-600" placeholder="Search templates (e.g. 'Real Estate')...">
                            <div class="pr-2 hidden sm:block">
                                <kbd class="px-2 py-1 bg-white/5 border border-white/10 rounded text-xs text-slate-400 font-mono">⌘ K</kbd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categories List (Glassmorphism Pills) -->
                <div class="flex flex-wrap gap-3 mb-10">
                    <button @click="activeCategory = 'All'" :class="activeCategory === 'All' ? 'bg-cyan-500/20 text-cyan-400 border-cyan-500/30' : 'bg-white/5 text-slate-300 border-white/10 hover:bg-white/10'" class="px-5 py-2 border rounded-full text-sm font-medium transition-colors">All Assets</button>
                    <button @click="activeCategory = 'Social Media'" :class="activeCategory === 'Social Media' ? 'bg-cyan-500/20 text-cyan-400 border-cyan-500/30' : 'bg-white/5 text-slate-300 border-white/10 hover:bg-white/10'" class="px-5 py-2 border rounded-full text-sm font-medium transition-colors">Social Media</button>
                    <button @click="activeCategory = 'Email Blueprint'" :class="activeCategory === 'Email Blueprint' ? 'bg-cyan-500/20 text-cyan-400 border-cyan-500/30' : 'bg-white/5 text-slate-300 border-white/10 hover:bg-white/10'" class="px-5 py-2 border rounded-full text-sm font-medium transition-colors">Email Blueprints</button>
                    <button @click="activeCategory = 'Ad Creative'" :class="activeCategory === 'Ad Creative' ? 'bg-cyan-500/20 text-cyan-400 border-cyan-500/30' : 'bg-white/5 text-slate-300 border-white/10 hover:bg-white/10'" class="px-5 py-2 border rounded-full text-sm font-medium transition-colors">Ad Creatives</button>
                    <button @click="activeCategory = 'Strategy Playbook'" :class="activeCategory === 'Strategy Playbook' ? 'bg-cyan-500/20 text-cyan-400 border-cyan-500/30' : 'bg-white/5 text-slate-300 border-white/10 hover:bg-white/10'" class="px-5 py-2 border rounded-full text-sm font-medium transition-colors">Playbooks</button>
                </div>

                <!-- Asset Grid (Dynamic) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <template x-for="asset in assets.filter(a => (activeCategory === 'All' || a.category === activeCategory) && (searchQuery === '' || a.title.toLowerCase().includes(searchQuery.toLowerCase()) || a.description.toLowerCase().includes(searchQuery.toLowerCase())))" :key="asset.id">
                        <div class="group relative bg-slate-900 border border-white/5 rounded-2xl overflow-hidden hover:border-cyan-500/50 hover:shadow-[0_0_30px_rgba(34,211,238,0.1)] transition-all duration-300 flex flex-col">
                            <!-- Image Preview -->
                            <div class="aspect-[4/5] bg-slate-800 relative overflow-hidden">
                                <img :src="asset.thumbnail_path" class="object-cover w-full h-full group-hover:scale-110 transition duration-700 mix-blend-luminosity group-hover:mix-blend-normal">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/20 to-transparent opacity-60"></div>
                                
                                <!-- Hover Quick Actions -->
                                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-[2px] opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-3">
                                    <a :href="asset.file_path" download class="p-3 bg-white hover:bg-cyan-400 rounded-full text-slate-900 transition-colors shadow-lg" title="Download">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </a>
                                    <a :href="asset.file_path" target="_blank" class="p-3 bg-white/20 hover:bg-white/30 backdrop-blur-md rounded-full text-white border border-white/30 transition-colors shadow-lg" title="Live Preview">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                </div>
                                
                                <!-- Badges -->
                                <div class="absolute top-4 left-4 flex flex-col gap-2">
                                    <span class="px-2 py-1 bg-slate-900/80 backdrop-blur-sm border border-white/10 text-xs text-white rounded font-medium" x-text="'.' + asset.extension">.PSD</span>
                                </div>
                            </div>
                            <!-- Details -->
                            <div class="p-5 flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start mb-2 gap-2">
                                        <h4 class="text-white font-semibold font-outfit leading-tight group-hover:text-cyan-400 transition-colors" x-text="asset.title">Diwali Mega Sale Kit</h4>
                                        <span class="text-[10px] px-2 py-1 rounded font-mono uppercase tracking-wider flex-shrink-0" :class="asset.price_tier === 'Free' ? 'bg-green-500/20 text-green-400 border border-green-500/20' : 'bg-slate-800 text-slate-300 border border-white/10'" x-text="asset.price_tier">Free</span>
                                    </div>
                                    <p class="text-slate-400 text-sm font-inter line-clamp-2" x-text="asset.description">12 Instagram Post Templates optimized for festive sales.</p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div class="mt-12 text-center">
                    <button class="px-8 py-3 bg-transparent border border-white/20 text-white rounded-full font-bold hover:bg-white/10 transition-colors inline-flex items-center gap-2">
                        <span>Browse All 5,000+ Assets</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </div>
        </section>       </section>

        <!-- AI Playground Laboratory -->
        <section class="py-20 bg-slate-900 border-y border-white/5 relative overflow-hidden">
            <!-- Decorative Background Glow -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-purple-500/20 rounded-full filter blur-[120px] opacity-50 pointer-events-none"></div>
            
            <div class="max-w-7xl mx-auto px-6 relative z-10">
                <div class="text-center mb-16">
                    <span class="text-purple-400 font-mono text-sm uppercase tracking-widest bg-purple-500/10 px-4 py-1.5 rounded-full border border-purple-500/20">The Laboratory</span>
                    <h2 class="text-4xl md:text-5xl font-bold text-white mt-6 font-outfit">AI Creative Playground</h2>
                    <p class="text-slate-400 mt-4 max-w-2xl mx-auto font-inter">Generate high-converting copy and stunning visuals in seconds using our proprietary models primed for marketing.</p>
                </div>

                <div class="flex flex-col lg:flex-row gap-12 items-stretch">
                    
                    <!-- Left: Text Generation (The Copywriter) -->
                    <div class="w-full lg:w-1/2 bg-navy-800/40 rounded-[2.5rem] border border-white/10 backdrop-blur-xl shadow-2xl relative group flex flex-col justify-between">
                        <div class="absolute inset-0 rounded-[2.5rem] ring-1 ring-inset ring-white/10 group-hover:ring-purple-500/50 transition-all duration-500 pointer-events-none"></div>
                        <livewire:ai-copywriter />
                    </div>

                    <!-- Right: Visual Generation (The Canvas) -->
                    <div class="w-full lg:w-1/2 bg-navy-800/40 rounded-[2.5rem] border border-white/10 backdrop-blur-xl shadow-2xl relative group flex flex-col justify-between">
                        <div class="absolute inset-0 rounded-[2.5rem] ring-1 ring-inset ring-white/10 group-hover:ring-cyan-500/50 transition-all duration-500 pointer-events-none"></div>
                        <livewire:ai-visual-canvas />
                    </div>

                </div>
            </div>
        </section>
        <!-- Client Collaboration Portal (The Private Office) -->
        <section class="py-20 bg-slate-50 border-t border-slate-200">
            <div class="max-w-6xl mx-auto px-6">
                
                <!-- Portal Header -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-10 gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest font-mono">In Review</span>
                        </div>
                        <h2 class="text-3xl font-bold text-slate-900 font-outfit">Project: <span class="text-indigo-600">Summer Launch '26</span></h2>
                        <p class="text-slate-500 font-inter mt-1">Client: Harsh Kumar / LPU</p>
                    </div>
                    <div class="flex gap-3">
                        <button class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-5 py-2.5 rounded-xl text-sm font-bold font-inter transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            Message Team
                        </button>
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold font-inter shadow-lg shadow-indigo-200 hover:shadow-indigo-300 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            New Request
                        </button>
                    </div>
                </div>

                <!-- Approval Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Asset Approval Card -->
                    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xl shadow-slate-200/40 flex flex-col group">
                        <!-- Asset Preview (Interactive) -->
                        <div class="p-8 border-b border-slate-100 bg-slate-50/50 flex-1 relative flex items-center justify-center group-hover:bg-slate-100/50 transition-colors cursor-crosshair">
                            <!-- Overlay tip for annotations -->
                            <div class="absolute top-4 right-4 bg-white/80 backdrop-blur-sm px-3 py-1 rounded-full border border-slate-200 text-xs font-medium text-slate-600 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">Click to annotate</div>
                            
                            <img src="https://images.unsplash.com/photo-1542744094-3a31f272c490?q=80&w=1000&auto=format&fit=crop" class="rounded-2xl shadow-md border border-slate-200/60 max-h-[300px] object-cover group-hover:scale-[1.02] transition-transform duration-500">
                            
                            <!-- Mock Annotation -->
                            <div class="absolute top-1/2 left-1/3 w-6 h-6 bg-red-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white text-xs font-bold ring-4 ring-red-500/20">1</div>
                        </div>
                        
                        <!-- Feedback Controls -->
                        <div class="p-8">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-xl text-slate-800 font-outfit">Instagram Ad Concept v2</h4>
                                <span class="bg-indigo-50 text-indigo-600 text-xs font-bold px-2.5 py-1 rounded-md border border-indigo-100">Review</span>
                            </div>
                            <p class="text-slate-500 text-sm mb-6 font-inter leading-relaxed">Targeting: Students in Jalandhar. Primary focus on B.Tech admissions highlighting placement records.</p>
                            
                            <div class="flex flex-col sm:flex-row items-center gap-4">
                                <button class="w-full sm:flex-1 py-3.5 bg-emerald-500 text-white rounded-2xl font-bold font-inter hover:bg-emerald-600 hover:shadow-lg hover:shadow-emerald-200 hover:-translate-y-0.5 transition-all flex justify-center items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Approve for Launch
                                </button>
                                <button class="w-full sm:flex-1 py-3.5 bg-white border border-slate-200 text-slate-700 rounded-2xl font-bold font-inter hover:bg-slate-50 hover:border-slate-300 transition-colors flex justify-center items-center gap-2">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Request Revision
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Feedback Loop Chat/Logs -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-xl shadow-slate-200/40 flex flex-col h-[600px] lg:h-auto">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                            <h3 class="font-bold text-xl text-slate-800 font-outfit flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                                Activity & Feedback
                            </h3>
                            <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded-md">Real-time</span>
                        </div>
                        
                        <!-- Chat Area -->
                        <div class="flex-1 overflow-y-auto space-y-6 mb-4 custom-scrollbar-light pr-2">
                            
                            <!-- System Log -->
                            <div class="flex justify-center">
                                <span class="bg-slate-100 text-slate-500 text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full">Creative Uploaded • Yesterday</span>
                            </div>

                            <!-- Admin Message -->
                            <div class="flex gap-4">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs flex-shrink-0">M</div>
                                <div>
                                    <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl rounded-tl-sm text-sm">
                                        <p class="font-bold text-indigo-700 font-outfit mb-1">MarketFlow Team</p>
                                        <p class="text-slate-600 font-inter leading-relaxed">Hi Harsh, v2 of the creative is ready for your review! We've updated the typography as requested in the last sync.</p>
                                    </div>
                                    <span class="text-[10px] text-slate-400 mt-1 ml-1 font-medium">10:42 AM</span>
                                </div>
                            </div>

                            <!-- Client Message -->
                            <div class="flex gap-4 flex-row-reverse">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs flex-shrink-0">HK</div>
                                <div>
                                    <div class="bg-indigo-50 border border-indigo-100/50 p-4 rounded-2xl rounded-tr-sm text-sm">
                                        <p class="font-bold text-slate-800 font-outfit mb-1">You (Client)</p>
                                        <p class="text-slate-700 font-inter leading-relaxed">Looks great! Just one minor thing: can we change the background blue in the top left <span class="bg-red-100 text-red-700 font-bold px-1 rounded">(Note 1)</span> to match our brand guidelines exactly?</p>
                                    </div>
                                    <div class="flex justify-end mt-1 mr-1 items-center gap-1">
                                        <span class="text-[10px] text-slate-400 font-medium">11:15 AM</span>
                                        <svg class="w-3 h-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Typing Indicator -->
                            <div class="flex gap-4">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs flex-shrink-0">M</div>
                                <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl rounded-tl-sm flex gap-1 items-center h-10">
                                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"></span>
                                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></span>
                                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                                </div>
                            </div>

                        </div>
                        
                        <!-- Input Box -->
                        <div class="relative mt-auto">
                            <input type="text" class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-5 pr-12 py-4 text-sm outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 font-inter placeholder:text-slate-400 transition-all shadow-inner" placeholder="Type a comment or drop a file...">
                            <button class="absolute right-3 top-3 p-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-md transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <style>
            .custom-scrollbar-light::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar-light::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.02); border-radius: 4px; }
            .custom-scrollbar-light::-webkit-scrollbar-thumb { background: rgba(0, 0, 0, 0.1); border-radius: 4px; }
            .custom-scrollbar-light::-webkit-scrollbar-thumb:hover { background: rgba(0, 0, 0, 0.2); }
        </style>

        <!-- Live Performance Tracking (The Financial Nerve Center) -->
        <section class="py-20 bg-slate-950 px-6 border-t border-white/5 relative overflow-hidden">
            <!-- Blueprint Grid Background -->
            <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.02)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.02)_1px,transparent_1px)] bg-[size:40px_40px] pointer-events-none opacity-20"></div>

            <div class="max-w-7xl mx-auto relative z-10">
                <!-- Header with Live Badge -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-10 gap-4">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-bold text-white tracking-tight font-outfit">Revenue & <span class="text-emerald-400">ROI Pulse</span></h2>
                        <p class="text-slate-400 mt-2 font-inter">Real-time performance tracking for your marketing capital.</p>
                    </div>
                    <div class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 px-4 py-2 rounded-full w-max">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                        </span>
                        <span class="text-emerald-400 text-xs font-bold uppercase tracking-widest font-mono">Live Syncing</span>
                    </div>
                </div>

                <!-- Chart Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main ROI Chart (Spans 2 columns) -->
                    <div class="lg:col-span-2 bg-slate-900/50 backdrop-blur-xl border border-white/10 p-6 md:p-8 rounded-[2.5rem] shadow-2xl relative group">
                        <div class="absolute inset-0 rounded-[2.5rem] bg-gradient-to-b from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                        
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4 relative z-10">
                            <div>
                                <h4 class="text-white font-bold font-outfit text-xl">Investment vs. Returns</h4>
                                <p class="text-xs text-slate-400 font-inter mt-1">Dual-axis view of ad spend and revenue generated (INR)</p>
                            </div>
                            <select class="bg-slate-800 border border-white/10 text-slate-300 text-xs font-medium rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-emerald-500/50 transition-shadow appearance-none cursor-pointer pr-8 bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center]">
                                <option>Current Month</option>
                                <option>Last Quarter</option>
                                <option>Year to Date</option>
                            </select>
                        </div>
                        
                        <!-- ApexChart Placeholder -->
                        <div id="roiPerformanceChart" class="min-h-[300px] w-full relative z-10"></div>
                    </div>

                    <!-- Side Widgets -->
                    <div class="space-y-6">
                        <!-- Efficiency Gauge -->
                        <div class="bg-slate-900/50 backdrop-blur-xl border border-white/10 p-8 rounded-[2.5rem] text-center shadow-2xl relative overflow-hidden group">
                            <!-- Subtle Glow -->
                            <div class="absolute -top-12 -right-12 w-32 h-32 bg-emerald-500/20 rounded-full blur-2xl group-hover:bg-emerald-500/30 transition-colors duration-500"></div>
                            
                            <p class="text-slate-400 text-sm mb-2 font-inter uppercase tracking-wider">Marketing Efficiency Ratio</p>
                            <div class="text-6xl font-black text-white font-outfit my-4 tracking-tight">4.8<span class="text-emerald-400 text-4xl">x</span></div>
                            
                            <div class="inline-flex items-center justify-center gap-1.5 px-3 py-1 bg-emerald-500/10 rounded-full">
                                <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                <span class="text-xs text-emerald-400 font-bold font-mono">0.4x from yesterday</span>
                            </div>
                        </div>
                        
                        <!-- Lead Velocity -->
                        <div class="bg-slate-900/50 backdrop-blur-xl border border-white/10 p-8 rounded-[2.5rem] shadow-2xl">
                            <div class="flex justify-between items-center mb-4">
                                <p class="text-slate-400 text-sm font-inter uppercase tracking-wider">Lead Velocity</p>
                                <span class="bg-cyan-500/20 text-cyan-400 text-[10px] px-2 py-0.5 rounded font-bold font-mono">ON TRACK</span>
                            </div>
                            
                            <!-- Speedometer/Progress Bar -->
                            <div class="relative pt-2">
                                <div class="flex mb-2 items-center justify-between">
                                    <div>
                                        <span class="text-xs font-semibold inline-block text-cyan-400 font-mono">
                                            72%
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-semibold inline-block text-slate-400 font-inter">
                                            360 / 500 Leads
                                        </span>
                                    </div>
                                </div>
                                <div class="overflow-hidden h-2.5 mb-4 text-xs flex rounded-full bg-slate-800 border border-white/5">
                                    <div style="width: 72%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-blue-500 to-cyan-400 relative">
                                        <!-- Animated pulse at the tip -->
                                        <div class="absolute right-0 top-0 bottom-0 w-4 bg-white/40 animate-pulse blur-[2px]"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <p class="text-xs text-slate-500 font-inter mt-1 leading-relaxed">Velocity indicates a strong upward trend via Instagram channels over the last 72 hours.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ROI Chart Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Formatting for the dual axis
                const formatCompactINR = (number) => {
                    if (number >= 100000) {
                        return '₹' + (number / 100000).toFixed(1) + 'L';
                    } else if (number >= 1000) {
                        return '₹' + (number / 1000).toFixed(1) + 'k';
                    }
                    return '₹' + number;
                };

                var roiOptions = {
                    series: [{
                        name: 'Ad Spend',
                        type: 'column',
                        data: [15000, 22000, 18000, 30000, 25000, 40000, 35000, 45000]
                    }, {
                        name: 'Revenue Generated',
                        type: 'area', // or line
                        data: [45000, 70000, 60000, 110000, 95000, 150000, 140000, 190000]
                    }],
                    chart: {
                        height: 350,
                        type: 'line',
                        stacked: false,
                        toolbar: { show: false },
                        fontFamily: 'Inter, sans-serif',
                        background: 'transparent',
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800,
                            dynamicAnimation: {
                                enabled: true,
                                speed: 350
                            }
                        }
                    },
                    stroke: {
                        width: [0, 4],
                        curve: 'smooth'
                    },
                    colors: ['#3b82f6', '#10b981'], // Blue for spend, Emerald for Revenue
                    fill: {
                        type: ['solid', 'gradient'],
                        gradient: {
                            shade: 'dark',
                            type: "vertical",
                            shadeIntensity: 1,
                            gradientToColors: ['#34d399'], // cyan/emerald finish
                            inverseColors: true,
                            opacityFrom: 0.45,
                            opacityTo: 0.05,
                            stops: [0, 100]
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            columnWidth: '35%',
                        }
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    xaxis: {
                        categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7', 'Week 8'],
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: {
                            style: { colors: '#94a3b8', fontFamily: 'Inter, sans-serif' }
                        }
                    },
                    yaxis: [
                        {
                            axisTicks: { show: false },
                            axisBorder: { show: false },
                            labels: {
                                style: { colors: '#3b82f6' },
                                formatter: function(val) { return formatCompactINR(val); }
                            },
                            title: {
                                text: "Ad Spend (₹)",
                                style: { color: '#3b82f6', fontWeight: 500 }
                            },
                        },
                        {
                            opposite: true,
                            axisTicks: { show: false },
                            axisBorder: { show: false },
                            labels: {
                                style: { colors: '#10b981' },
                                formatter: function(val) { return formatCompactINR(val); }
                            },
                            title: {
                                text: "Revenue (₹)",
                                style: { color: '#10b981', fontWeight: 500 }
                            }
                        }
                    ],
                    grid: {
                        borderColor: 'rgba(255,255,255,0.05)',
                        strokeDashArray: 4,
                        xaxis: { lines: { show: true } },
                        yaxis: { lines: { show: true } },
                    },
                    tooltip: {
                        theme: 'dark',
                        shared: true,
                        intersect: false,
                        y: {
                            formatter: function (y) {
                                if (typeof y !== "undefined") {
                                    return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(y);
                                }
                                return y;
                            }
                        }
                    },
                    legend: {
                        labels: { colors: '#94a3b8' },
                        position: 'top',
                        horizontalAlign: 'right'
                    }
                };

                if(document.querySelector("#roiPerformanceChart")) {
                    var roiChart = new ApexCharts(document.querySelector("#roiPerformanceChart"), roiOptions);
                    roiChart.render();
                }
            });
        </script>
        <!-- Automated Marketing Funnel Builder (The Logic Hub) -->
        <section class="py-20 bg-slate-50 overflow-hidden border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-6">
                
                <!-- Header with Controls -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-6">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-black text-slate-900 font-outfit">Funnel <span class="text-indigo-600">Architect</span></h2>
                        <p class="text-slate-500 font-inter mt-2">Map out your customer journey with logic-driven automation.</p>
                    </div>
                    <div class="flex bg-white p-1.5 rounded-2xl shadow-sm border border-slate-200">
                        <button class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-bold shadow-md hover:bg-indigo-700 transition-colors">Edit Logic</button>
                        <button class="px-6 py-2.5 text-slate-600 hover:text-slate-900 text-sm font-bold transition-colors">Simulate Traffic</button>
                    </div>
                </div>

                <!-- The Visualizer Canvas -->
                <div class="relative w-full h-[600px] bg-white rounded-[3rem] border border-slate-200 shadow-xl overflow-hidden cursor-crosshair">
                    <!-- Subtle Dot Grid Background -->
                    <div class="absolute inset-0 bg-[radial-gradient(#e5e7eb_2px,transparent_2px)] [background-size:24px_24px] opacity-60"></div>
                    
                    <!-- SVG Connecting Lines -->
                    <svg class="absolute inset-0 w-full h-full pointer-events-none">
                        <!-- Line from FB Ads to Landing Page -->
                        <path d="M 220 280 Q 380 280 480 180" stroke="url(#gradient-line)" stroke-width="4" fill="transparent" class="animate-dash opacity-80" />
                        <!-- Line from Landing Page to Email Sequence -->
                        <path d="M 700 180 Q 800 180 850 350" stroke="url(#gradient-line)" stroke-width="4" fill="transparent" class="animate-dash opacity-80" />
                        <!-- Line from FB Ads to Conversion (Direct) -->
                        <path d="M 220 320 Q 350 450 850 450" stroke="#cbd5e1" stroke-width="2" stroke-dasharray="8 8" fill="transparent" class="opacity-50" />
                        
                        <defs>
                            <linearGradient id="gradient-line" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#4F46E5" />
                                <stop offset="100%" stop-color="#06B6D4" />
                            </linearGradient>
                        </defs>
                    </svg>

                    <!-- Funnel Nodes (Placed with absolute positioning) -->
                    
                    <!-- Step 1: Traffic Source -->
                    <div class="absolute top-[240px] left-[5%] md:left-[10%] w-56 bg-white border-2 border-indigo-500 p-5 rounded-3xl shadow-xl z-10 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 cursor-move">
                        <div class="absolute -top-3 -right-3 w-6 h-6 bg-indigo-500 text-white rounded-full flex items-center justify-center text-xs font-bold ring-4 ring-white shadow-sm">1</div>
                        <div class="flex items-center gap-4">
                            <div class="bg-indigo-50 p-3 rounded-2xl text-2xl border border-indigo-100">📱</div>
                            <div>
                                <h4 class="text-base font-bold text-slate-800 font-outfit">Meta Ads</h4>
                                <p class="text-xs text-slate-500 font-inter mt-0.5">Traffic Source</p>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between items-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Live Spend</span>
                            <span class="text-xs font-bold text-slate-800">₹12.5k / day</span>
                        </div>
                    </div>

                    <!-- Step 2: Landing Page -->
                    <div class="absolute top-[120px] left-[45%] md:left-[45%] w-56 bg-white border border-slate-200 p-5 rounded-3xl shadow-lg z-10 hover:border-cyan-400 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-move">
                        <div class="absolute -top-3 -right-3 w-6 h-6 bg-cyan-500 text-white rounded-full flex items-center justify-center text-xs font-bold ring-4 ring-white shadow-sm">2</div>
                        <div class="flex items-center gap-4">
                            <div class="bg-cyan-50 p-3 rounded-2xl text-2xl border border-cyan-100">📄</div>
                            <div>
                                <h4 class="text-base font-bold text-slate-800 font-outfit">Lead Capture</h4>
                                <p class="text-xs text-slate-500 font-inter mt-0.5">Opt-in Page</p>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between items-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Conv. Rate</span>
                            <span class="text-xs font-bold text-cyan-600 bg-cyan-50 px-2 py-0.5 rounded">32.4%</span>
                        </div>
                    </div>

                    <!-- Step 3: Nurture Sequence -->
                    <div class="absolute top-[320px] left-[70%] md:left-[75%] w-56 bg-white border border-slate-200 p-5 rounded-3xl shadow-lg z-10 hover:border-purple-400 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-move">
                        <div class="absolute -top-3 -right-3 w-6 h-6 bg-purple-500 text-white rounded-full flex items-center justify-center text-xs font-bold ring-4 ring-white shadow-sm">3</div>
                        <div class="flex items-center gap-4">
                            <div class="bg-purple-50 p-3 rounded-2xl text-2xl border border-purple-100">💬</div>
                            <div>
                                <h4 class="text-base font-bold text-slate-800 font-outfit">WhatsApp & Email Nurture Flow</h4>
                                <p class="text-xs text-slate-500 font-inter mt-0.5">WhatsApp & Email Flow</p>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between items-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Open Rate</span>
                            <span class="text-xs font-bold text-slate-800">48%</span>
                        </div>
                    </div>

                     <!-- Floating Logic Gate -->
                     <div class="absolute top-[200px] left-[30%] md:left-[32%] bg-slate-900 text-white p-2 rounded-xl shadow-xl z-20 hover:scale-110 hover:bg-slate-800 transition-all cursor-pointer flex items-center gap-2 group border border-slate-700">
                        <div class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></div>
                        <span class="text-[10px] font-bold px-1 font-mono tracking-wider">IF OPT-IN</span>
                        <div class="hidden group-hover:block absolute -top-10 left-1/2 -translate-x-1/2 bg-white text-slate-800 text-[10px] font-bold py-1 px-3 rounded shadow-lg whitespace-nowrap">Edit Condition</div>
                     </div>
                     
                     <!-- Conversion Bubble (Simulation) -->
                     <div class="absolute top-[170px] left-[60%] bg-emerald-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-[8px] font-bold shadow-[0_0_15px_rgba(16,185,129,0.5)] border-2 border-white z-20" style="animation: travel 10s linear infinite;">+1</div>

                </div>
            </div>
        </section>

        <style>
            /* Animation for the SVG paths */
            .animate-dash {
                stroke-dasharray: 15;
                animation: dash 30s linear infinite;
            }
            @keyframes dash {
                to { stroke-dashoffset: -1000; }
            }
            @keyframes travel {
                0% { top: 120px; left: 45%; opacity: 0; }
                10% { opacity: 1; }
                50% { top: 180px; left: 60%; }
                90% { opacity: 1; }
                100% { top: 320px; left: 75%; opacity: 0; }
            }
        </style>

        <!-- Agency E-Store & Templates Shop (E-Portal Products) -->
        <section id="shop-section" class="py-24 bg-slate-950 border-t border-white/5 relative">
            <div class="absolute -top-40 -left-40 w-96 h-96 bg-neon-cyan/10 rounded-full blur-[100px] pointer-events-none"></div>
            <div class="max-w-7xl mx-auto px-6 relative z-10">
                <div class="text-center mb-16">
                    <span class="text-neon-cyan font-mono text-sm uppercase tracking-widest bg-neon-cyan/10 px-4 py-1.5 rounded-full border border-neon-cyan/20">Marketplace</span>
                    <h2 class="text-4xl md:text-5xl font-bold text-white mt-6 font-outfit">Premium Tools & Resources</h2>
                    <p class="text-slate-400 mt-4 max-w-2xl mx-auto font-inter">Accelerate your campaigns with our production-ready templates, custom scripts, and professional add-ons.</p>
                </div>
                
                <livewire:product-shop />
            </div>
        </section>
        <!-- Dynamic Testimonials & Brand Social Proof (The Trust Engine) -->
        <section class="py-24 bg-slate-950 overflow-hidden relative">
            <!-- Glow effect -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-indigo-500/10 rounded-full blur-[100px] pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-6 relative z-10">
                
                <!-- Section Header -->
                <div class="text-center mb-20">
                    <h2 class="text-4xl md:text-5xl font-black text-white font-outfit">Trusted by the <span class="text-indigo-500">Next Generation</span> of Brands</h2>
                    <p class="text-slate-400 mt-4 font-inter text-lg">Join 3,000+ businesses scaling with MarketFlow.</p>
                </div>

                <!-- Infinite Logo Marquee -->
                <div class="relative flex overflow-x-hidden group mb-24 border-y border-white/5 bg-white/[0.02]">
                    <!-- Gradient Overlays for smooth fade -->
                    <div class="absolute left-0 top-0 bottom-0 w-32 bg-gradient-to-r from-slate-950 to-transparent z-10"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-32 bg-gradient-to-l from-slate-950 to-transparent z-10"></div>
                    
                    <div class="py-10 animate-marquee whitespace-nowrap flex items-center gap-24">
                        <!-- Simulated Logos (using text/SVGs for demo) -->
                        <div class="text-2xl font-black text-white/30 hover:text-white transition-colors cursor-default font-outfit">ACME Corp</div>
                        <div class="text-2xl font-black text-white/30 hover:text-white transition-colors cursor-default font-outfit">GlobalTech</div>
                        <div class="text-2xl font-black text-white/30 hover:text-white transition-colors cursor-default font-outfit">LPU Startups</div>
                        <div class="text-2xl font-black text-white/30 hover:text-white transition-colors cursor-default font-outfit">Nexus Labs</div>
                        <div class="text-2xl font-black text-white/30 hover:text-white transition-colors cursor-default font-outfit">Nova Media</div>
                        <div class="text-2xl font-black text-white/30 hover:text-white transition-colors cursor-default font-outfit">Stratos</div>
                        <!-- Duplicate for seamless loop -->
                        <div class="text-2xl font-black text-white/30 hover:text-white transition-colors cursor-default font-outfit">ACME Corp</div>
                        <div class="text-2xl font-black text-white/30 hover:text-white transition-colors cursor-default font-outfit">GlobalTech</div>
                        <div class="text-2xl font-black text-white/30 hover:text-white transition-colors cursor-default font-outfit">LPU Startups</div>
                        <div class="text-2xl font-black text-white/30 hover:text-white transition-colors cursor-default font-outfit">Nexus Labs</div>
                        <div class="text-2xl font-black text-white/30 hover:text-white transition-colors cursor-default font-outfit">Nova Media</div>
                        <div class="text-2xl font-black text-white/30 hover:text-white transition-colors cursor-default font-outfit">Stratos</div>
                    </div>
                </div>

                <!-- Testimonial Masonry Grid -->
                <div class="columns-1 md:columns-2 lg:columns-3 gap-8 space-y-8 max-w-6xl mx-auto">
                    
                    <!-- Single Testimonial Card 1 -->
                    <div class="break-inside-avoid bg-white/5 border border-white/10 p-8 rounded-[2rem] hover:bg-white/10 hover:border-indigo-500/30 hover:shadow-[0_0_30px_rgba(99,102,241,0.1)] transition-all duration-500 group relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10">
                            <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                        </div>
                        <div class="flex items-center gap-4 mb-6 relative z-10">
                            <div class="relative">
                                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=150&auto=format&fit=crop" class="w-14 h-14 rounded-full border-2 border-indigo-500 object-cover">
                                <div class="absolute -bottom-1 -right-1 bg-slate-900 rounded-full p-0.5">
                                    <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-white font-bold font-outfit text-lg">Mehak Rajpoot</h4>
                                <p class="text-slate-400 text-xs font-inter mt-0.5">Director @ CreativeFlow</p>
                            </div>
                        </div>
                        <p class="text-slate-300 leading-relaxed font-inter relative z-10 text-sm">
                            "MarketFlow completely changed how we handle client reporting. The <strong class="text-white">ROI Tracking in INR</strong> is a game-changer for our Indian clients. We've seen a 40% increase in retention!"
                        </p>
                        <div class="mt-6 flex items-center justify-between relative z-10">
                            <div class="flex text-amber-400 text-sm">★★★★★</div>
                            <span class="text-slate-500 text-[10px] font-mono uppercase tracking-wider">Verified Business</span>
                        </div>
                    </div>

                    <!-- Single Testimonial Card 2 (Video Placeholder) -->
                    <div class="break-inside-avoid bg-slate-800 border border-indigo-500/30 p-1.5 rounded-[2.5rem] hover:shadow-[0_0_40px_rgba(99,102,241,0.2)] transition-all duration-500 group relative">
                        <div class="relative rounded-[2rem] overflow-hidden aspect-[4/5] cursor-pointer">
                            <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?q=80&w=1000&auto=format&fit=crop" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 opacity-80 mix-blend-luminosity group-hover:mix-blend-normal group-hover:opacity-100">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent opacity-80"></div>
                            
                            <!-- Play Button -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center border border-white/40 group-hover:bg-white group-hover:scale-110 transition-all duration-300 shadow-xl">
                                    <svg class="w-6 h-6 text-white group-hover:text-indigo-600 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </div>
                            
                            <div class="absolute bottom-0 left-0 right-0 p-8">
                                <h4 class="text-white font-bold font-outfit text-xl">"How we hit 5x ROI in 30 days."</h4>
                                <div class="flex items-center gap-3 mt-4">
                                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=150&auto=format&fit=crop" class="w-8 h-8 rounded-full border border-white/30">
                                    <div>
                                        <p class="text-white text-sm font-bold">Rahul Sharma</p>
                                        <p class="text-slate-300 text-[10px]">Founder, TechLaunch</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Single Testimonial Card 3 -->
                    <div class="break-inside-avoid bg-white/5 border border-white/10 p-8 rounded-[2rem] hover:bg-white/10 hover:border-cyan-500/30 hover:shadow-[0_0_30px_rgba(6,182,212,0.1)] transition-all duration-500 group relative overflow-hidden">
                        <div class="flex items-center gap-4 mb-6 relative z-10">
                            <div class="relative">
                                <div class="w-14 h-14 rounded-full border-2 border-cyan-500 bg-cyan-900 text-cyan-400 flex items-center justify-center font-bold text-xl font-outfit">SJ</div>
                                <div class="absolute -bottom-1 -right-1 bg-slate-900 rounded-full p-0.5">
                                    <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-white font-bold font-outfit text-lg">Sneha Joshi</h4>
                                <p class="text-slate-400 text-xs font-inter mt-0.5">Marketing Lead @ EdTech India</p>
                            </div>
                        </div>
                        <p class="text-slate-300 leading-relaxed font-inter relative z-10 text-sm">
                            "The automated funnel builder is incredible. We mapped out our entire student acquisition journey in 20 minutes. The <strong class="text-cyan-400">AI Copywriter</strong> alone saves us hours every week."
                        </p>
                        <div class="mt-6 flex items-center justify-between relative z-10">
                            <div class="flex text-amber-400 text-sm">★★★★★</div>
                            <span class="text-slate-500 text-[10px] font-mono uppercase tracking-wider">Verified Business</span>
                        </div>
                    </div>

                    <!-- Single Testimonial Card 4 -->
                    <div class="break-inside-avoid bg-indigo-600 p-8 rounded-[2rem] shadow-xl group relative overflow-hidden text-white">
                        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0IiBoZWlnaHQ9IjQiPjxyZWN0IHdpZHRoPSI0IiBoZWlnaHQ9IjQiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] opacity-50"></div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-6">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                <span class="font-bold font-inter text-sm">@marketflow_user</span>
                            </div>
                            <p class="font-inter text-lg font-medium leading-relaxed mb-6">
                                Just tried the Client Collaboration portal in MarketFlow. No more messy email chains for creative approvals. This is the new standard for agency-client relationships. 🚀🔥
                            </p>
                            <p class="text-indigo-200 text-xs font-mono">10:42 AM • Oct 14, 2026</p>
                        </div>
                    </div>

                </div>

                <!-- Platform Ratings -->
                <div class="mt-20 flex flex-wrap justify-center items-center gap-12 lg:gap-24 border-t border-white/5 pt-12">
                    <div class="text-center">
                        <div class="text-3xl font-black text-white font-outfit mb-1">4.9/5</div>
                        <div class="flex text-amber-400 text-sm justify-center mb-2">★★★★★</div>
                        <div class="text-slate-400 text-xs font-bold uppercase tracking-widest">G2 Crowd</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-white font-outfit mb-1">5.0/5</div>
                        <div class="flex text-amber-400 text-sm justify-center mb-2">★★★★★</div>
                        <div class="text-slate-400 text-xs font-bold uppercase tracking-widest">Product Hunt</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-white font-outfit mb-1">#1</div>
                        <div class="flex text-amber-400 text-sm justify-center mb-2">★★★★★</div>
                        <div class="text-slate-400 text-xs font-bold uppercase tracking-widest">Marketing Tech '26</div>
                    </div>
                </div>

            </div>
        </section>

        <!-- CMS Blog & Insights Portal (News & Content) -->
        <section id="blog-section" class="py-24 bg-slate-950 border-t border-white/5 relative">
            <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-neon-purple/10 rounded-full blur-[100px] pointer-events-none"></div>
            <div class="max-w-7xl mx-auto px-6 relative z-10">
                <div class="text-center mb-16">
                    <span class="text-neon-purple font-mono text-sm uppercase tracking-widest bg-neon-purple/10 px-4 py-1.5 rounded-full border border-neon-purple/20">Resources</span>
                    <h2 class="text-4xl md:text-5xl font-bold text-white mt-6 font-outfit">Latest Insights & Strategy</h2>
                    <p class="text-slate-400 mt-4 max-w-2xl mx-auto font-inter">Get the latest updates on growth marketing techniques, compliance guidelines, and industry news.</p>
                </div>

                <livewire:blog-catalog />
            </div>
        </section>

        <!-- Subscription Pricing Tiers -->
        <section class="py-24 bg-white border-t border-slate-200" x-data="{ yearly: false }">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 font-outfit">Scalable Pricing for <span class="text-indigo-600">Every Brand</span></h2>
                <p class="text-slate-500 font-inter max-w-2xl mx-auto mb-12">Start for free, upgrade when you need more power. All plans come with a 14-day money-back guarantee.</p>
                
                <!-- Toggle Switch -->
                <div class="flex items-center justify-center gap-4 mb-16">
                    <span class="text-sm font-bold transition-colors cursor-pointer" :class="yearly ? 'text-slate-400' : 'text-slate-900'" @click="yearly = false">Monthly</span>
                    <button class="w-14 h-8 bg-indigo-600 rounded-full p-1 relative transition-colors focus:outline-none" @click="yearly = !yearly" :class="yearly ? 'bg-emerald-500' : 'bg-indigo-600'">
                        <div class="bg-white w-6 h-6 rounded-full shadow-sm transform transition-transform duration-300" :class="yearly ? 'translate-x-6' : 'translate-x-0'"></div>
                    </button>
                    <span class="text-sm font-bold transition-colors cursor-pointer" :class="yearly ? 'text-slate-900' : 'text-slate-400'" @click="yearly = true">
                        Yearly <span class="text-emerald-500 ml-1 bg-emerald-50 px-2 py-0.5 rounded-full text-xs">(Save 20%)</span>
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center max-w-5xl mx-auto text-left">
                    
                    <!-- Basic Plan -->
                    <div class="p-8 bg-slate-50 rounded-[2.5rem] border border-slate-200 hover:border-indigo-300 transition-colors duration-300">
                        <h3 class="text-2xl font-bold text-slate-900 font-outfit">Starter</h3>
                        <p class="text-slate-500 text-sm mt-2 font-inter h-10">Perfect for freelancers and solo marketers.</p>
                        <div class="mt-6 flex items-baseline">
                            <span class="text-5xl font-black text-slate-900 font-outfit">₹0</span>
                            <span class="text-slate-500 ml-2 font-medium">/mo</span>
                        </div>
                        <ul class="mt-8 space-y-4 text-sm text-slate-600 font-inter">
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                1 Active Campaign
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                5 AI Generations / month
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Basic Asset Library
                            </li>
                            <li class="flex items-center gap-3 text-slate-400">
                                <svg class="w-5 h-5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Funnel Builder
                            </li>
                            <li class="flex items-center gap-3 text-slate-400">
                                <svg class="w-5 h-5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Custom Domains
                            </li>
                        </ul>
                        <button class="w-full mt-10 py-4 border-2 border-slate-900 text-slate-900 rounded-2xl font-bold hover:bg-slate-900 hover:text-white transition-colors font-inter">Get Started Free</button>
                    </div>

                    <!-- Pro Plan (The Hero) -->
                    <div class="p-10 bg-slate-900 rounded-[2.5rem] shadow-2xl shadow-indigo-200 relative scale-105 z-10 border border-slate-800">
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest shadow-lg shadow-indigo-500/30">Most Popular</div>
                        <h3 class="text-2xl font-bold text-white font-outfit">Professional</h3>
                        <p class="text-slate-400 text-sm mt-2 font-inter h-10">For growing businesses needing serious automation.</p>
                        <div class="mt-6 flex items-baseline text-white">
                            <span class="text-5xl font-black font-outfit" x-text="yearly ? '₹1,999' : '₹2,499'">₹2,499</span>
                            <span class="text-slate-400 ml-2 font-medium" x-text="yearly ? '/mo, billed yearly' : '/mo'">/mo</span>
                        </div>
                        <ul class="mt-8 space-y-4 text-sm text-slate-300 font-inter">
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-cyan-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                10 Active Campaigns
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-cyan-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Unlimited AI Generations
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-cyan-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Funnel Builder Access
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-cyan-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Real-time ROI Tracking
                            </li>
                            <li class="flex items-center gap-3 text-slate-500">
                                <svg class="w-5 h-5 text-slate-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                White-label Client Portals
                            </li>
                        </ul>
                        <button class="w-full mt-10 py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-500/30 hover:bg-indigo-500 hover:shadow-indigo-500/50 hover:-translate-y-0.5 transition-all font-inter">Go Pro</button>
                    </div>

                    <!-- Agency Plan -->
                    <div class="p-8 bg-slate-50 rounded-[2.5rem] border border-slate-200 hover:border-indigo-300 transition-colors duration-300">
                        <h3 class="text-2xl font-bold text-slate-900 font-outfit">Agency</h3>
                        <p class="text-slate-500 text-sm mt-2 font-inter h-10">For agencies managing multiple client accounts.</p>
                        <div class="mt-6 flex items-baseline text-slate-900">
                            <span class="text-5xl font-black font-outfit" x-text="yearly ? '₹6,399' : '₹7,999'">₹7,999</span>
                            <span class="text-slate-500 ml-2 font-medium" x-text="yearly ? '/mo, billed yearly' : '/mo'">/mo</span>
                        </div>
                        <ul class="mt-8 space-y-4 text-sm text-slate-600 font-inter">
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Unlimited Campaigns
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Custom AI Model Training
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                API Access
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                White-label Client Portals
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                24/7 Priority Support
                            </li>
                        </ul>
                        <button class="w-full mt-10 py-4 bg-white border border-slate-200 text-slate-900 rounded-2xl font-bold hover:bg-slate-50 transition-colors shadow-sm font-inter">Contact Sales</button>
                    </div>

                </div>
            </div>
        </section>


        <!-- Alpine.js (Livewire v3 automatically injects Alpine, manual script tag removed to prevent conflicts) -->
    </body>
</html>
