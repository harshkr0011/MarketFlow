<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card 1: LTV -->
        <div class="bg-navy-800/40 backdrop-blur-xl border border-navy-700/50 rounded-2xl p-6 shadow-2xl relative overflow-hidden group hover:border-neon-cyan/30 transition-all duration-300">
            <!-- Decorative Glow -->
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-neon-cyan/10 rounded-full blur-xl group-hover:bg-neon-cyan/20 transition-all duration-300"></div>
            
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-neon-cyan/10 rounded-lg text-neon-cyan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h2 class="text-lg font-semibold font-outfit text-white">LTV (Lifetime Value)</h2>
            </div>
            
            <p class="text-slate-400 text-sm">Average LTV across all active subscriptions:</p>
            <p class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-neon-cyan to-indigo-400 mt-3 font-outfit tracking-tight">₹12,450</p>
            <p class="text-xs text-slate-500 mt-3 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                Based on the last 6 months of agency data
            </p>
        </div>
        
        <!-- Card 2: Pending Invoices -->
        <div class="bg-navy-800/40 backdrop-blur-xl border border-navy-700/50 rounded-2xl p-6 shadow-2xl relative overflow-hidden group hover:border-neon-purple/30 transition-all duration-300">
            <!-- Decorative Glow -->
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-neon-purple/10 rounded-full blur-xl group-hover:bg-neon-purple/20 transition-all duration-300"></div>
            
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-neon-purple/10 rounded-lg text-neon-purple">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <h2 class="text-lg font-semibold font-outfit text-white">Pending Invoices</h2>
            </div>
            
            <p class="text-slate-400 text-sm">Requires manual follow-up:</p>
            <ul class="mt-4 space-y-3">
                <li class="flex justify-between items-center bg-navy-900/40 border border-navy-700/30 rounded-xl p-3 hover:bg-navy-900/60 transition-colors">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                        <span class="text-slate-300 font-medium text-sm">Agency X <span class="text-xs text-slate-500 font-normal">(- Pro Tier)</span></span>
                    </div>
                    <span class="font-mono text-sm font-bold text-red-400">₹2,999</span>
                </li>
                <li class="flex justify-between items-center bg-navy-900/40 border border-navy-700/30 rounded-xl p-3 hover:bg-navy-900/60 transition-colors">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                        <span class="text-slate-300 font-medium text-sm">Student Y <span class="text-xs text-slate-500 font-normal">(- Starter)</span></span>
                    </div>
                    <span class="font-mono text-sm font-bold text-red-400">₹499</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- The actual ApexCharts would be rendered here via Filament Widgets -->
    <div class="mt-8">
        @livewire(\Filament\Widgets\StatsOverviewWidget::class)
    </div>
</x-filament-panels::page>

