<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <a href="/admin" class="text-xs font-semibold text-neon-cyan hover:text-cyan-300 transition-colors flex items-center gap-1.5 mb-2 group">
                    <svg class="w-3.5 h-3.5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Admin Panel
                </a>
                <h2 class="font-bold text-3xl text-white leading-tight font-outfit flex items-center gap-3">
                    <svg class="w-8 h-8 text-indigo-400 drop-shadow-[0_0_8px_rgba(129,140,248,0.5)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    {{ __('User & Subscription Hub') }}
                </h2>
            </div>
            <div class="text-xs font-mono text-slate-300 bg-navy-800/80 border border-navy-700/80 px-4 py-2 rounded-xl backdrop-blur-md flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Live Database: <span class="text-neon-cyan font-bold">{{ $users->count() }} accounts</span>
            </div>
        </div>
    </x-slot>

    @php
        $totalUsers = $users->count();
        $activeSubs = 0;
        $totalAssets = 0;
        $totalFunnels = 0;
        foreach($users as $u) {
            $hasSub = \Illuminate\Support\Facades\DB::table('subscriptions')->where('user_id', $u->id)->where('stripe_status', 'active')->exists();
            if($hasSub) {
                $activeSubs++;
            }
            $totalAssets += $u->assets_count;
            $totalFunnels += $u->campaigns_count;
        }
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if (session('status'))
                <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3.5 rounded-2xl flex items-center gap-2.5 animate-fadeIn shadow-lg shadow-emerald-500/5">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-medium text-sm">{{ session('status') }}</span>
                </div>
            @endif

            <!-- Mini Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <div class="bg-navy-800/40 backdrop-blur-xl border border-navy-700/50 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-indigo-500/30 transition-all duration-300">
                    <div class="absolute -right-6 -top-6 w-20 h-20 bg-indigo-500/5 rounded-full blur-xl group-hover:bg-indigo-500/10 transition-all duration-300"></div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Users</p>
                    <p class="text-3xl font-extrabold text-white mt-2 font-outfit">{{ $totalUsers }}</p>
                </div>
                <div class="bg-navy-800/40 backdrop-blur-xl border border-navy-700/50 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-neon-purple/30 transition-all duration-300">
                    <div class="absolute -right-6 -top-6 w-20 h-20 bg-neon-purple/5 rounded-full blur-xl group-hover:bg-neon-purple/10 transition-all duration-300"></div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Premium Subscriptions</p>
                    <p class="text-3xl font-extrabold text-neon-purple mt-2 font-outfit">{{ $activeSubs }}</p>
                </div>
                <div class="bg-navy-800/40 backdrop-blur-xl border border-navy-700/50 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-neon-cyan/30 transition-all duration-300">
                    <div class="absolute -right-6 -top-6 w-20 h-20 bg-neon-cyan/5 rounded-full blur-xl group-hover:bg-neon-cyan/10 transition-all duration-300"></div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Platform Assets</p>
                    <p class="text-3xl font-extrabold text-neon-cyan mt-2 font-outfit">{{ $totalAssets }}</p>
                </div>
                <div class="bg-navy-800/40 backdrop-blur-xl border border-navy-700/50 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-pink-500/30 transition-all duration-300">
                    <div class="absolute -right-6 -top-6 w-20 h-20 bg-pink-500/5 rounded-full blur-xl group-hover:bg-pink-500/10 transition-all duration-300"></div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Funnels</p>
                    <p class="text-3xl font-extrabold text-pink-400 mt-2 font-outfit">{{ $totalFunnels }}</p>
                </div>
            </div>

            <!-- Data Table Card -->
            <div class="bg-navy-800/40 backdrop-blur-xl border border-navy-700/50 overflow-hidden shadow-2xl rounded-2xl relative">
                <!-- Glowing border accent -->
                <div class="absolute inset-0 border border-indigo-500/10 rounded-2xl pointer-events-none"></div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-navy-950/80 border-b border-navy-700">
                                <th class="py-4.5 px-6 font-outfit text-xs font-semibold text-slate-400 uppercase tracking-wider">User</th>
                                <th class="py-4.5 px-6 font-outfit text-xs font-semibold text-slate-400 uppercase tracking-wider">Plan Status</th>
                                <th class="py-4.5 px-6 font-outfit text-xs font-semibold text-slate-400 uppercase tracking-wider">Assets</th>
                                <th class="py-4.5 px-6 font-outfit text-xs font-semibold text-slate-400 uppercase tracking-wider">Funnels</th>
                                <th class="py-4.5 px-6 font-outfit text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-navy-700/50">
                            @foreach($users as $user)
                            <tr class="hover:bg-navy-800/30 transition-colors duration-150">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-500 via-indigo-600 to-purple-600 flex items-center justify-center text-white font-bold shadow-lg shadow-indigo-500/10 select-none">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-white font-semibold text-sm flex items-center gap-2">
                                                {{ $user->name }}
                                                @if($user->isAdmin())
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-pink-500/20 text-pink-400 border border-pink-500/30 shadow-[0_0_10px_rgba(236,72,153,0.15)] uppercase tracking-wider">ADMIN</span>
                                                @endif
                                            </div>
                                            <div class="text-slate-400 text-xs mt-0.5">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    @php
                                        $sub = \Illuminate\Support\Facades\DB::table('subscriptions')->where('user_id', $user->id)->where('stripe_status', 'active')->first();
                                    @endphp
                                    
                                    @if($sub)
                                        @if($sub->type == 'pro')
                                            <div class="inline-flex flex-col">
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-neon-purple/20 text-purple-200 border border-neon-purple/40 shadow-[0_0_12px_rgba(168,85,247,0.25)]">Pro Plan</span>
                                                @if($sub->trial_ends_at && \Carbon\Carbon::parse($sub->trial_ends_at)->isFuture())
                                                    <div class="text-[10px] text-purple-300 mt-1.5 ml-1 font-mono flex items-center gap-1">
                                                        <span class="w-1 h-1 rounded-full bg-neon-purple animate-ping"></span>
                                                        Trial ends: {{ \Carbon\Carbon::parse($sub->trial_ends_at)->diffForHumans() }}
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif($sub->type == 'agency')
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-neon-cyan/20 text-cyan-200 border border-neon-cyan/40 shadow-[0_0_12px_rgba(34,211,238,0.25)]">Agency Plan</span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-slate-800 text-slate-300 border border-slate-700">Starter Plan</span>
                                        @endif
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-slate-800 text-slate-300 border border-slate-700">Starter Plan</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2 text-slate-300 font-mono text-sm">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        {{ $user->assets_count }}
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2 text-slate-300 font-mono text-sm">
                                        <svg class="w-4 h-4 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        {{ $user->campaigns_count }}
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2.5">
                                        <!-- Upgrade Button -->
                                        @if(!$sub || $sub->type != 'pro')
                                            <form method="POST" action="{{ route('admin.users.upgrade', $user) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="p-2 bg-indigo-500/10 hover:bg-indigo-500/20 border border-indigo-500/30 rounded-xl text-indigo-400 hover:text-indigo-300 hover:scale-105 transition-all group relative" title="Manual Pro Upgrade (14 Days)">
                                                    <svg class="w-4 h-4 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M5 19l7-7 7 7"></path></svg>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <!-- Impersonate Button -->
                                        @if(!$user->isAdmin())
                                            <a href="{{ route('admin.users.impersonate', $user) }}" class="p-2 bg-neon-purple/10 hover:bg-neon-purple/20 border border-neon-purple/30 rounded-xl text-purple-300 hover:text-purple-200 hover:scale-105 transition-all flex items-center justify-center group" title="Login as User">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
