<div class="grid grid-cols-1 lg:grid-cols-12 min-h-[500px]" x-data="{
    showNewPin: false,
    newPinX: 0,
    newPinY: 0,
    newPinComment: '',
    hoveredPinId: null,
    handleImageClick(e) {
        const rect = e.currentTarget.getBoundingClientRect();
        this.newPinX = ((e.clientX - rect.left) / rect.width * 100).toFixed(1);
        this.newPinY = ((e.clientY - rect.top) / rect.height * 100).toFixed(1);
        this.newPinComment = '';
        this.showNewPin = true;
    },
    submitPin() {
        if (!this.newPinComment.trim()) return;
        $wire.addAnnotation(this.newPinX, this.newPinY, this.newPinComment);
        this.showNewPin = false;
        this.newPinComment = '';
    }
}">
    @if(!$workspaceId)
        <div class="col-span-12 flex flex-col items-center justify-center p-12 text-center text-slate-500">
            <svg class="w-16 h-16 mb-4 text-indigo-400 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <h4 class="text-white font-semibold mb-1">No Active Workspace Selected</h4>
            <p class="text-sm text-slate-400 max-w-sm">Please create or select a Workspace in the Admin Panel to start collaborating with your clients.</p>
        </div>
    @else
        <!-- Left: Collaboration Chat (lg:col-span-5) -->
        <div class="lg:col-span-5 flex flex-col border-r border-navy-700/60 bg-navy-900/10">
            <!-- Campaign Status & Approval Workflow -->
            <div class="bg-navy-900/40 p-4 border-b border-navy-700/60 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Active Campaign</div>
                        <div class="text-xs font-bold text-white flex items-center gap-1.5 mt-0.5">
                            <span class="text-neon-cyan">📁</span> {{ $campaignTitle }}
                        </div>
                    </div>
                    <div>
                        @if($campaignStatus === 'Idea')
                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full font-mono uppercase bg-yellow-500/10 border border-yellow-500/30 text-yellow-400">Idea</span>
                        @elseif($campaignStatus === 'Design')
                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full font-mono uppercase bg-blue-500/10 border border-blue-500/30 text-blue-400">Design</span>
                        @elseif($campaignStatus === 'Review')
                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full font-mono uppercase bg-purple-500/10 border border-purple-500/30 text-purple-400">Review</span>
                        @elseif($campaignStatus === 'Published')
                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full font-mono uppercase bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-bold">Approved</span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2 border-t border-navy-800">
                    <button wire:click="updateCampaignStatus('Published')" class="flex-1 py-1.5 bg-emerald-600/90 hover:bg-emerald-500 text-white text-xs font-bold rounded-lg transition-colors flex items-center justify-center gap-1 shadow-lg shadow-emerald-600/10">
                        👍 Approve Launch
                    </button>
                    <button wire:click="updateCampaignStatus('Design')" class="flex-1 py-1.5 bg-amber-600/90 hover:bg-amber-500 text-white text-xs font-bold rounded-lg transition-colors flex items-center justify-center gap-1 shadow-lg shadow-amber-600/10">
                        🔄 Request Revision
                    </button>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3 max-h-[360px]" id="chat-messages" x-data="{ scroll: () => { $el.scrollTop = $el.scrollHeight; } }" x-init="scroll()" @message-sent.window="setTimeout(() => scroll(), 100)">
                @forelse($messages as $message)
                    <div class="flex {{ $message['user_id'] === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="flex max-w-[85%] {{ $message['user_id'] === auth()->id() ? 'flex-row-reverse' : 'flex-row' }}">
                            <div class="h-6 w-6 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white text-[10px] font-bold shrink-0 {{ $message['user_id'] === auth()->id() ? 'ml-2' : 'mr-2' }}">
                                {{ substr($message['user']['name'] ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <div class="text-[9px] text-slate-400 mb-0.5 {{ $message['user_id'] === auth()->id() ? 'text-right' : 'text-left' }}">
                                    {{ $message['user']['name'] ?? 'User' }} • {{ \Carbon\Carbon::parse($message['created_at'])->diffForHumans() }}
                                </div>
                                <div class="p-2.5 rounded-xl text-xs shadow-md {{ $message['user_id'] === auth()->id() ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-navy-800 text-slate-200 rounded-tl-none border border-navy-700' }}">
                                    @if(str_contains($message['content'], '📍 [ANNOTATION]'))
                                        <span class="text-neon-cyan font-semibold">📍 Review Annotation:</span>
                                        <p class="mt-0.5 text-slate-300 italic">{{ preg_replace('/📍 \[ANNOTATION\] \(x:[\d\.]+%,\s*y:[\d\.]%\):\s*/i', '', $message['content']) }}</p>
                                    @else
                                        {!! nl2br(e($message['content'])) !!}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-500 mt-8">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-40 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                        <p class="text-xs">No conversation history. Send a message to start!</p>
                    </div>
                @endforelse
            </div>

            <!-- Input Form -->
            <div class="p-3 border-t border-navy-700/60 bg-navy-800/20">
                <form wire:submit.prevent="sendMessage" class="flex gap-2 relative">
                    <input 
                        wire:model="newMessage" 
                        type="text" 
                        class="w-full bg-navy-950/80 border border-navy-700 rounded-xl py-2 pl-3 pr-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="Type a message..."
                    >
                    <button type="submit" class="absolute right-1 top-1 bottom-1 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg w-7 h-7 flex items-center justify-center transition-colors disabled:opacity-50" wire:loading.attr="disabled">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Pinned Visual Review (lg:col-span-7) -->
        <div class="lg:col-span-7 flex flex-col p-4 bg-navy-900/5">
            <div class="mb-3">
                <h4 class="text-white font-bold text-sm font-outfit flex items-center gap-1.5">
                    🎨 Visual Campaign Creative Review
                </h4>
                <p class="text-[11px] text-slate-400 mt-0.5">Click directly on the image below to place a numbered annotation pin with revision feedback.</p>
            </div>

            <!-- Annotation Target Box -->
            <div class="flex-1 flex items-center justify-center relative bg-navy-950 rounded-xl overflow-hidden border border-navy-700/80 p-2 select-none group min-h-[300px]">
                <div class="relative inline-block w-full h-auto cursor-crosshair" @click="handleImageClick">
                    <img src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=800&auto=format&fit=crop&q=80" 
                        class="w-full h-full max-h-[320px] object-cover rounded-lg pointer-events-none" 
                        alt="Creative Review Billboard" />

                    <!-- Overlay Pins -->
                    @foreach($annotations as $index => $pin)
                        <div class="absolute w-5 h-5 bg-neon-cyan border border-slate-950 rounded-full flex items-center justify-center text-[10px] text-slate-950 font-black shadow-lg cursor-pointer transform -translate-x-1/2 -translate-y-1/2 hover:scale-125 transition-transform duration-150"
                            style="left: {{ $pin['x'] }}%; top: {{ $pin['y'] }}%;"
                            @mouseenter="hoveredPinId = {{ $pin['id'] }}"
                            @mouseleave="hoveredPinId = null">
                            {{ $index + 1 }}
                            
                            <!-- Tooltip Card -->
                            <div x-show="hoveredPinId === {{ $pin['id'] }}" 
                                class="absolute bottom-6 left-1/2 transform -translate-x-1/2 w-48 bg-navy-950/95 border border-neon-cyan/50 text-white rounded-lg p-2.5 text-[10px] shadow-2xl z-50 pointer-events-none"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0">
                                <div class="font-bold text-neon-cyan flex justify-between items-center mb-1">
                                    <span>Pin #{{ $index + 1 }}</span>
                                    <span class="text-slate-400 text-[8px]">{{ $pin['user'] }}</span>
                                </div>
                                <p class="text-slate-200 italic font-medium leading-normal">"{{ $pin['text'] }}"</p>
                            </div>
                        </div>
                    @endforeach

                    <!-- New Pin Input Card (Interactive Popup) -->
                    <div x-show="showNewPin" 
                        class="absolute bg-navy-950/95 border border-indigo-500/80 rounded-xl p-3 shadow-2xl z-50 w-64 transform -translate-x-1/2 -translate-y-1/2"
                        :style="`left: ${newPinX}%; top: ${newPinY}%;`"
                        @click.stop>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-bold text-indigo-400">📍 Pinned Feedback</span>
                            <button @click="showNewPin = false" class="text-slate-400 hover:text-white text-xs">✕</button>
                        </div>
                        <input 
                            x-model="newPinComment" 
                            type="text" 
                            class="w-full bg-navy-900 border border-navy-700 rounded-lg py-1.5 px-2 text-xs text-white focus:outline-none focus:ring-1 focus:ring-indigo-400" 
                            placeholder="Describe revision (e.g. Logo is too small)"
                            @keydown.enter.prevent="submitPin"
                        >
                        <div class="flex justify-end gap-1.5 mt-2.5">
                            <button @click="showNewPin = false" class="px-2 py-1 bg-navy-800 text-slate-300 text-[9px] rounded font-semibold hover:bg-navy-700">Cancel</button>
                            <button @click="submitPin" class="px-2.5 py-1 bg-indigo-600 text-white text-[9px] rounded font-semibold hover:bg-indigo-500 shadow-md shadow-indigo-600/10">Add Comment</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

