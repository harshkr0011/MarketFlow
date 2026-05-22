<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Form Container -->
        <div class="bg-navy-800/40 backdrop-blur-xl border border-navy-700/50 rounded-2xl p-6 shadow-2xl relative overflow-hidden group hover:border-indigo-500/30 transition-all duration-300">
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-indigo-500/10 rounded-full blur-xl group-hover:bg-indigo-500/20 transition-all duration-300"></div>
            
            <form wire:submit="save" class="space-y-6">
                {{ $this->form }}
        
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-medium text-sm rounded-xl shadow-[0_0_15px_rgba(99,102,241,0.4)] hover:shadow-[0_0_20px_rgba(99,102,241,0.6)] transition-all duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Save Prompt Settings
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Info Card: How it works -->
        <div class="bg-navy-800/40 backdrop-blur-xl border border-navy-700/50 rounded-2xl p-6 shadow-2xl relative overflow-hidden group hover:border-neon-cyan/30 transition-all duration-300">
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-neon-cyan/10 rounded-full blur-xl group-hover:bg-neon-cyan/20 transition-all duration-300"></div>
            
            <div class="flex items-center gap-2.5 mb-3 text-neon-cyan">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="text-lg font-semibold font-outfit text-white">How it works</h3>
            </div>
            
            <p class="text-sm text-slate-300 leading-relaxed">
                The system prompt is the invisible context given to the AI (e.g., OpenAI API) before the user's actual prompt. 
                By adjusting this, you can fine-tune the platform's outputs (tone, style, strictness) without deploying any code changes.
            </p>
        </div>
    </div>
</x-filament-panels::page>

