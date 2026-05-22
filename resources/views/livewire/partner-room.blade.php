<div class="space-y-6">
    <!-- Top Grid: RFP Submission & Active Requests -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Create RFP Brief Form -->
        <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-slate-100 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Post Partner RFP Brief
            </h3>
            
            <form wire:submit.prevent="createRfp" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">RFP Title</label>
                    <input type="text" wire:model="title" 
                           placeholder="e.g., Q3 Video Creator Brief" 
                           class="w-full bg-slate-950/60 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/80 transition" />
                    @error('title') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Detailed Scope / Brief</label>
                    <textarea wire:model="description" rows="3"
                              placeholder="Outline project deliverables, dimensions, and specifications..." 
                              class="w-full bg-slate-950/60 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/80 transition"></textarea>
                    @error('description') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Budget Limit</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-slate-500 text-sm">₹</span>
                            <input type="number" wire:model="budget_limit" 
                                   placeholder="250000" 
                                   class="w-full bg-slate-950/60 border border-slate-800 rounded-xl pl-7 pr-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/80 transition" />
                        </div>
                        @error('budget_limit') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Deadline</label>
                        <input type="date" wire:model="deadline" 
                               class="w-full bg-slate-950/60 border border-slate-800 rounded-xl px-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/80 transition" />
                        @error('deadline') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-600 to-violet-600 text-slate-100 rounded-xl font-medium text-sm hover:from-indigo-500 hover:to-violet-500 hover:shadow-lg hover:shadow-indigo-500/20 active:scale-[0.98] transition">
                    Dispatch Brief to Network
                </button>
            </form>
        </div>

        <!-- Right: List of Dispatched RFPs (Middle + Right columns) -->
        <div class="lg:col-span-2 bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 rounded-2xl p-6 shadow-xl flex flex-col">
            <h3 class="text-lg font-semibold text-slate-100 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Dispatched Proposals & Vendor Briefs
            </h3>

            <div class="space-y-4 overflow-y-auto max-h-[360px] pr-2">
                @forelse($rfps as $rfp)
                    <div class="bg-slate-950/40 border border-slate-800/60 rounded-xl p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 transition hover:border-slate-700/60">
                        <div class="space-y-1 max-w-md">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-slate-200">{{ $rfp->title }}</span>
                                <span class="px-2 py-0.5 text-[10px] font-medium bg-indigo-500/10 text-indigo-400 rounded-full border border-indigo-500/20">Active RFP</span>
                            </div>
                            <p class="text-xs text-slate-400 line-clamp-2">{{ $rfp->description }}</p>
                            <div class="flex items-center gap-4 text-[10px] text-slate-500 mt-1">
                                <span>Target Campaign: <strong class="text-slate-400">{{ $rfp->campaign ? $rfp->campaign->title : 'N/A' }}</strong></span>
                                <span>•</span>
                                <span>Deadline: <strong class="text-slate-400">{{ \Carbon\Carbon::parse($rfp->deadline)->format('M d, Y') }}</strong></span>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Budget Limit</span>
                            <span class="text-lg font-bold text-slate-200">₹{{ number_format($rfp->budget_limit) }}</span>
                            <span class="text-[10px] text-indigo-400 font-medium">{{ $rfp->proposals->count() }} Bids Received</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-slate-500 text-sm">
                        No active vendor requests found. Post a brief to begin.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Bottom: Live Bids & Quotes Panel -->
    <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 rounded-2xl p-6 shadow-xl">
        <h3 class="text-lg font-semibold text-slate-100 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-6 2h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Incoming Vendor Quotes & RFP Submissions
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/40 text-slate-400 text-xs uppercase tracking-wider font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Vendor Agency</th>
                        <th class="px-6 py-4">Target RFP</th>
                        <th class="px-6 py-4">Bid Amount</th>
                        <th class="px-6 py-4">Attachment</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($proposals as $proposal)
                        <tr class="hover:bg-slate-950/10 transition">
                            <td class="px-6 py-4 font-semibold text-slate-200">
                                {{ $proposal->partner ? $proposal->partner->company_name : 'N/A' }}
                                <span class="block text-[10px] text-slate-500 font-normal">{{ $proposal->partner ? $proposal->partner->email : '' }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-400">
                                {{ $proposal->rfp ? $proposal->rfp->title : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-200">
                                ₹{{ number_format($proposal->bid_amount) }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="#" class="text-xs text-indigo-400 hover:text-indigo-300 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Review proposal.pdf
                                </a>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($proposal->status === 'Awarded')
                                    <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-500/10 text-emerald-400 rounded-full border border-emerald-500/20">Awarded</span>
                                @elseif($proposal->status === 'Declined')
                                    <span class="px-2.5 py-1 text-[10px] font-bold bg-rose-500/10 text-rose-400 rounded-full border border-rose-500/20">Declined</span>
                                @else
                                    <span class="px-2.5 py-1 text-[10px] font-bold bg-amber-500/10 text-amber-400 rounded-full border border-amber-500/20">Pending Review</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-slate-500">
                                No proposals submitted yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
