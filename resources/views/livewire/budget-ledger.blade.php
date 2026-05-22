<div class="space-y-6">
    
    <!-- Top KPI Cards: Budget Status Summary -->
    @if($budgets->count() > 0)
        @php
            $budget = $budgets->first();
            $approvedSum = $budget->drawdowns->where('status', 'Approved')->sum('amount_requested');
            $pendingSum = $budget->drawdowns->where('status', 'Pending')->sum('amount_requested');
            $remaining = $budget->total_amount - $approvedSum - $pendingSum;
            $spentPct = min(100, ($approvedSum / $budget->total_amount) * 100);
            $pendingPct = min(100, ($pendingSum / $budget->total_amount) * 100);
        @endphp
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Master Budget -->
            <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Allocated Budget</span>
                <span class="text-2xl font-bold text-slate-100 mt-2">₹{{ number_format($budget->total_amount) }}</span>
                <span class="text-[10px] text-slate-500 mt-1">Fiscal Year: {{ $budget->fiscal_year }}</span>
            </div>

            <!-- Approved Spend -->
            <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Approved Drawdown Spend</span>
                <span class="text-2xl font-bold text-emerald-400 mt-2">₹{{ number_format($approvedSum) }}</span>
                <div class="w-full bg-slate-950 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $spentPct }}%"></div>
                </div>
            </div>

            <!-- Pending Allocations -->
            <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pending Drawdowns</span>
                <span class="text-2xl font-bold text-amber-400 mt-2">₹{{ number_format($pendingSum) }}</span>
                <div class="w-full bg-slate-950 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $pendingPct }}%"></div>
                </div>
            </div>

            <!-- Remaining Balance -->
            <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Remaining Unallocated</span>
                <span class="text-2xl font-bold text-indigo-400 mt-2">₹{{ number_format($remaining) }}</span>
                <span class="text-[10px] text-slate-500 mt-1">Available for Campaign seeding</span>
            </div>
        </div>
    @endif

    <!-- Mid Grid: Request Drawdown & History -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Request Drawdown Form -->
        <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-slate-100 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Request Campaign Drawdown
            </h3>
            
            <form wire:submit.prevent="requestDrawdown" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Select Active Budget</label>
                    <select wire:model="selected_budget_id" 
                            class="w-full bg-slate-950/60 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/80 transition">
                        @foreach($budgets as $b)
                            <option value="{{ $b->id }}">{{ $b->scope }} ({{ $b->fiscal_year }}) - Limit: ₹{{ number_format($b->total_amount) }}</option>
                        @endforeach
                    </select>
                    @error('selected_budget_id') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Target Marketing Campaign</label>
                    <select wire:model="selected_campaign_id" 
                            class="w-full bg-slate-950/60 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/80 transition">
                        @foreach($campaigns as $c)
                            <option value="{{ $c->id }}">{{ $c->title }} (Status: {{ $c->status }})</option>
                        @endforeach
                    </select>
                    @error('selected_campaign_id') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Requested Spend Amount</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-500 text-sm">₹</span>
                        <input type="number" wire:model="amount_requested" 
                               placeholder="150000" 
                               class="w-full bg-slate-950/60 border border-slate-800 rounded-xl pl-7 pr-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/80 transition" />
                    </div>
                    @error('amount_requested') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-600 to-violet-600 text-slate-100 rounded-xl font-medium text-sm hover:from-indigo-500 hover:to-violet-500 hover:shadow-lg hover:shadow-indigo-500/20 active:scale-[0.98] transition">
                    Submit Drawdown Request
                </button>
            </form>
        </div>

        <!-- Drawdown Logs Table -->
        <div class="lg:col-span-2 bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 rounded-2xl p-6 shadow-xl flex flex-col">
            <h3 class="text-lg font-semibold text-slate-100 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                Spend Drawdown Ledger Logs
            </h3>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950/40 text-slate-400 text-xs uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Campaign Scope</th>
                            <th class="px-4 py-3">Requested Amount</th>
                            <th class="px-4 py-3">Date Requested</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($drawdowns as $drawdown)
                            <tr class="hover:bg-slate-950/10 transition">
                                <td class="px-4 py-3 font-semibold text-slate-200">
                                    {{ $drawdown->campaign ? $drawdown->campaign->title : 'N/A' }}
                                    <span class="block text-[10px] text-slate-500 font-normal">Source: {{ $drawdown->budget ? $drawdown->budget->scope : 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-200">
                                    ₹{{ number_format($drawdown->amount_requested) }}
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-400">
                                    {{ $drawdown->created_at->format('M d, Y • h:i A') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($drawdown->status === 'Approved')
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-500/10 text-emerald-400 rounded-full border border-emerald-500/20">Approved</span>
                                    @elseif($drawdown->status === 'Rejected')
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-rose-500/10 text-rose-400 rounded-full border border-rose-500/20">Rejected</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-500/10 text-amber-400 rounded-full border border-amber-500/20">Pending Approval</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-10 text-slate-500">
                                    No spend requests filed.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
