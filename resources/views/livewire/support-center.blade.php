<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Left Pane: Tickets (Col 5) -->
    <div class="lg:col-span-5 space-y-6">
        <!-- Tickets Header -->
        <div class="bg-navy-800/60 border border-navy-700/50 p-6 rounded-2xl space-y-4">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-bold text-white font-outfit">Support Tickets</h3>
                    <p class="text-[10px] text-slate-400">Track and manage your requests</p>
                </div>
                <button wire:click="$toggle('isRaisingTicket')" 
                    class="px-3 py-1.5 bg-gradient-to-r from-neon-purple to-indigo-600 hover:from-neon-purple hover:to-indigo-500 text-white rounded-xl text-[10px] font-bold transition">
                    {{ $isRaisingTicket ? 'Cancel' : '➕ Raise Ticket' }}
                </button>
            </div>

            @if($isRaisingTicket)
                <!-- Raise Ticket Form -->
                <form wire:submit.prevent="raiseTicket" class="space-y-4 pt-2 border-t border-navy-700/50">
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Subject</label>
                        <input type="text" wire:model="subject" placeholder="Summarize your issue..." 
                            class="w-full bg-navy-950 border border-navy-750 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-neon-purple">
                        @error('subject') <span class="text-[10px] text-red-400">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Priority</label>
                        <select wire:model="priority" class="w-full bg-navy-950 border border-navy-750 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-neon-purple">
                            <option value="Low" class="bg-navy-950 text-slate-200">Low Priority</option>
                            <option value="Medium" class="bg-navy-950 text-slate-200">Medium Priority</option>
                            <option value="High" class="bg-navy-950 text-slate-200">High Priority</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Detailed Description</label>
                        <textarea wire:model="description" rows="4" placeholder="Provide details, error messages, or context..." 
                            class="w-full bg-navy-950 border border-navy-750 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-neon-purple"></textarea>
                        @error('description') <span class="text-[10px] text-red-400">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" 
                        class="w-full py-2 bg-gradient-to-r from-neon-purple to-indigo-600 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-neon-purple/10 uppercase tracking-wider">
                        Submit Support Ticket
                    </button>
                </form>
            @endif
        </div>

        <!-- Ticket List Feed -->
        <div class="space-y-3">
            @forelse($tickets as $ticket)
                <div class="bg-gradient-to-br from-navy-800/80 to-navy-900/80 border border-navy-700/50 p-4 rounded-xl space-y-3">
                    <div class="flex justify-between items-start gap-2">
                        <div>
                            <h4 class="text-xs font-bold text-white leading-tight font-outfit">{{ $ticket->subject }}</h4>
                            <span class="text-[9px] text-slate-500 font-mono">Created {{ $ticket->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex gap-1.5 shrink-0">
                            <span class="px-2 py-0.5 text-[8px] font-bold rounded uppercase tracking-wider
                                {{ $ticket->priority === 'High' ? 'bg-red-500/10 text-red-400 border border-red-500/25' : ($ticket->priority === 'Medium' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/25' : 'bg-slate-500/10 text-slate-400 border border-slate-500/25') }}">
                                {{ $ticket->priority }}
                            </span>
                            <span class="px-2 py-0.5 text-[8px] font-bold rounded uppercase tracking-wider
                                {{ $ticket->status === 'Open' ? 'bg-red-500/10 text-red-400 border border-red-500/25' : ($ticket->status === 'In Progress' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/25' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/25') }}">
                                {{ $ticket->status }}
                            </span>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-400 leading-relaxed font-inter">{{ $ticket->description }}</p>
                </div>
            @empty
                <div class="bg-navy-800/20 border border-navy-700/30 p-8 rounded-xl text-center">
                    <span class="text-2xl">🎫</span>
                    <h4 class="text-xs font-bold text-slate-400 mt-2">No tickets found</h4>
                    <p class="text-[10px] text-slate-500 mt-1">If you have technical or billing issues, raise a ticket above.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Right Pane: AI Chatbot (Col 7) -->
    <div class="lg:col-span-7 bg-navy-800/60 border border-navy-700/50 rounded-2xl flex flex-col h-[520px] shadow-2xl relative overflow-hidden">
        <!-- Chatbot Header -->
        <div class="px-6 py-4 bg-navy-950/50 border-b border-navy-750 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-xl bg-neon-purple/10 border border-neon-purple/20 text-base">🤖</span>
                <div>
                    <h3 class="text-xs font-bold text-white font-outfit">MarketFlow Smart Assistant</h3>
                    <span class="text-[9px] text-emerald-400 font-bold block mt-0.5">● Connected to Gemini Core</span>
                </div>
            </div>
            <span class="text-[9px] font-mono text-slate-500 uppercase tracking-widest bg-navy-900 border border-navy-850 px-2 py-0.5 rounded">AI Sandbox</span>
        </div>

        <!-- Chat Feed -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4">
            @foreach($chatHistory as $msg)
                <div class="flex {{ $msg['sender'] === 'user' ? 'justify-end' : 'justify-start' }} gap-3 items-start">
                    @if($msg['sender'] === 'ai')
                        <div class="w-7 h-7 rounded-lg bg-neon-purple/10 border border-neon-purple/20 flex items-center justify-center text-xs shrink-0 mt-0.5">🤖</div>
                    @endif
                    <div class="max-w-[75%] p-3.5 rounded-2xl text-xs leading-relaxed
                        {{ $msg['sender'] === 'user' 
                            ? 'bg-gradient-to-r from-neon-purple to-indigo-600 text-white rounded-tr-none shadow-lg' 
                            : 'bg-navy-950 border border-navy-800 text-slate-200 rounded-tl-none' }}">
                        <p class="whitespace-pre-line">{{ $msg['text'] }}</p>
                    </div>
                </div>
            @endforeach

            @if($isChatLoading)
                <div class="flex justify-start gap-3 items-start animate-pulse">
                    <div class="w-7 h-7 rounded-lg bg-neon-purple/10 border border-neon-purple/20 flex items-center justify-center text-xs shrink-0 mt-0.5">🤖</div>
                    <div class="bg-navy-950 border border-navy-800 p-3.5 rounded-2xl rounded-tl-none max-w-[50%] flex gap-1.5 items-center">
                        <div class="w-1.5 h-1.5 bg-neon-purple rounded-full animate-bounce"></div>
                        <div class="w-1.5 h-1.5 bg-neon-purple rounded-full animate-bounce" style="animation-delay: 0.15s"></div>
                        <div class="w-1.5 h-1.5 bg-neon-purple rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Chat Input Form -->
        <form wire:submit.prevent="sendChatMessage" class="p-4 bg-navy-950/40 border-t border-navy-750">
            <div class="relative flex items-center">
                <input wire:model="chatInput" type="text" placeholder="Type a message or ask marketing questions..." 
                    class="w-full bg-navy-950 border border-navy-750 rounded-xl pl-4 pr-12 py-3 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-neon-purple transition-all">
                <button type="submit" 
                    class="absolute right-2.5 p-2 bg-neon-purple/15 border border-neon-purple/35 hover:bg-neon-purple/30 text-neon-purple rounded-lg transition-all">
                    ➔
                </button>
            </div>
        </form>
    </div>
</div>
