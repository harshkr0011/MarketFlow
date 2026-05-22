<div class="space-y-6">
    <!-- Header Controls -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-navy-800/40 backdrop-blur-xl border border-navy-700/50 p-6 rounded-2xl">
        <div>
            <h3 class="text-sm font-bold text-white font-outfit">AI Marketing Automation & Predictions</h3>
            <p class="text-[10px] text-slate-400">Deep learning predictions and targeted recommendations for your active workspace.</p>
        </div>
        
        <button wire:click="generateRecommendations" wire:loading.attr="disabled"
            class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-neon-purple to-neon-cyan hover:from-neon-purple/80 hover:to-neon-cyan/80 text-navy-900 font-black rounded-xl text-xs transition shadow-lg shadow-neon-purple/15">
            <span wire:loading.remove>⚡ Regenerate Predictions</span>
            <span wire:loading class="flex items-center gap-1.5">
                <svg class="animate-spin h-3 w-3 text-navy-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Analyzing Pipeline...
            </span>
        </button>
    </div>

    <!-- Recommendations Container -->
    <div class="bg-gradient-to-br from-navy-800 to-navy-900 border border-navy-700 rounded-2xl p-8 shadow-2xl relative overflow-hidden">
        <!-- Background decorative glows -->
        <div class="absolute -right-24 -top-24 w-48 h-48 bg-neon-purple/10 rounded-full blur-3xl"></div>
        <div class="absolute -left-24 -bottom-24 w-48 h-48 bg-neon-cyan/10 rounded-full blur-3xl"></div>

        <div wire:loading.class="opacity-45 pointer-events-none" class="transition-opacity duration-200">
            <!-- Custom Styled Markdown Renderer -->
            <div class="recommendations-content text-xs text-slate-300 leading-relaxed space-y-6">
                
                {!! \Illuminate\Support\Str::markdown($recommendationsMarkdown) !!}
            </div>
        </div>
    </div>
</div>
