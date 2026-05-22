<div class="relative z-10 p-6">
    <div class="flex items-center mb-4">
        <div class="bg-navy-700 p-2 rounded-lg mr-3">
            <svg class="w-6 h-6 text-neon-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        </div>
        <h3 class="text-lg font-bold text-white font-outfit">AI Copywriter (Powered by Gemini)</h3>
    </div>
    <p class="text-gray-400 mb-4 text-sm">Need a catchy headline or an email sequence? Let AI generate it for you.</p>
    
    <div class="space-y-4">
        <div class="flex gap-4">
            <select wire:model="tone" class="bg-navy-900/50 border border-navy-700 rounded-lg py-2 px-3 text-white focus:outline-none focus:ring-2 focus:ring-neon-purple">
                <option value="professional">Professional</option>
                <option value="witty">Witty</option>
                <option value="urgent">Urgent</option>
                <option value="friendly">Friendly</option>
                <option value="persuasive">Persuasive</option>
            </select>
        </div>

        <!-- Quick Templates Library -->
        <div class="mb-4">
            <span class="text-xs text-gray-400 font-semibold block mb-2 uppercase tracking-wider">Quick Fill Templates:</span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                <button type="button" @click="$wire.set('prompt', 'Write a persuasive Facebook ad description for a Delhi-based company selling zero-waste bamboo products, targeting young professionals.'); $wire.set('tone', 'persuasive')" class="text-left p-2.5 bg-navy-900/40 border border-navy-700/80 rounded-lg hover:border-neon-purple hover:bg-navy-700/30 text-slate-300 hover:text-white transition-all">
                    <div class="font-bold text-neon-cyan mb-0.5">🌱 Eco Product Ad (Delhi)</div>
                    <div class="text-[10px] text-gray-500 line-clamp-1">Delhi zero-waste bamboo products...</div>
                </button>
                <button type="button" @click="$wire.set('prompt', 'Draft a witty email sequence announcing the beta launch of a developer productivity tool created by a startup in Bangalore.'); $wire.set('tone', 'witty')" class="text-left p-2.5 bg-navy-900/40 border border-navy-700/80 rounded-lg hover:border-neon-purple hover:bg-navy-700/30 text-slate-300 hover:text-white transition-all">
                    <div class="font-bold text-neon-purple mb-0.5">🚀 Tech Startup Launch (BLR)</div>
                    <div class="text-[10px] text-gray-500 line-clamp-1">Bangalore developer tool email...</div>
                </button>
                <button type="button" @click="$wire.set('prompt', 'Write an urgent Instagram caption with hashtags for a summer fashion collection launch of a boutique based in Mumbai.'); $wire.set('tone', 'urgent')" class="text-left p-2.5 bg-navy-900/40 border border-navy-700/80 rounded-lg hover:border-neon-purple hover:bg-navy-700/30 text-slate-300 hover:text-white transition-all">
                    <div class="font-bold text-pink-400 mb-0.5">👗 Fashion Collection (Mumbai)</div>
                    <div class="text-[10px] text-gray-500 line-clamp-1">Mumbai fashion collection launch...</div>
                </button>
                <button type="button" @click="$wire.set('prompt', 'Generate a supportive and friendly reminder email for shoppers who left premium leather shoes in their checkout cart, offering a 10% coupon.'); $wire.set('tone', 'friendly')" class="text-left p-2.5 bg-navy-900/40 border border-navy-700/80 rounded-lg hover:border-neon-purple hover:bg-navy-700/30 text-slate-300 hover:text-white transition-all">
                    <div class="font-bold text-emerald-400 mb-0.5">🛒 Cart Recovery Coupon</div>
                    <div class="text-[10px] text-gray-500 line-clamp-1">Friendly reminder with 10% discount...</div>
                </button>
            </div>
        </div>

        <div class="relative">
            <textarea wire:model="prompt" rows="3" class="w-full bg-navy-900/50 border border-navy-700 rounded-lg py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-neon-purple focus:border-transparent transition-all" placeholder="E.g. Generate 5 ad headlines for a tech startup..."></textarea>
            
            <button wire:click="generate" wire:loading.attr="disabled" class="absolute right-2 bottom-3 bg-gradient-to-r from-neon-purple to-indigo-600 hover:from-neon-purple hover:to-indigo-500 text-white rounded p-2 transition-all shadow-lg hover:shadow-neon-purple/20 flex items-center justify-center min-w-[40px]">
                <span wire:loading.remove wire:target="generate">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </span>
                <span wire:loading wire:target="generate" class="animate-spin">
                    <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </span>
            </button>
        </div>
        
        @error('prompt') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    @if($generatedCopy)
        <div class="mt-6 p-5 bg-navy-900/80 border border-neon-cyan/30 rounded-2xl relative space-y-4">
            <div class="flex items-center justify-between border-b border-navy-800 pb-3">
                <h4 class="text-neon-cyan text-sm font-semibold flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Generated Copy Result
                </h4>
                <div class="flex items-center gap-2">
                    <button onclick="navigator.clipboard.writeText(`{{ addslashes($generatedCopy) }}`)" class="text-slate-400 hover:text-white transition-colors p-1" title="Copy to clipboard">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                </div>
            </div>

            <div class="text-slate-200 text-sm whitespace-pre-wrap font-inter leading-relaxed p-4 bg-navy-950/40 border border-navy-800 rounded-xl">
                {!! nl2br(e($generatedCopy)) !!}
            </div>

            <!-- Translate Actions -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-400 font-bold font-inter">🌍 Localize:</span>
                    <select wire:model="targetLang" class="bg-navy-950 border border-navy-700 rounded-lg py-1.5 px-3 text-xs text-white focus:outline-none focus:ring-1 focus:ring-neon-purple font-inter">
                        <option value="en_in">English (India)</option>
                        <option value="hinglish">Hinglish (India)</option>
                        <option value="hi">Hindi (India)</option>
                        <option value="en">English (US)</option>
                        <option value="es">Spanish (Latin America)</option>
                    </select>
                </div>
                <button wire:click="translateCopy" class="px-4 py-2 bg-gradient-to-r from-neon-purple to-indigo-600 hover:from-neon-purple hover:to-indigo-500 text-white text-xs font-semibold rounded-lg shadow-md hover:shadow-neon-purple/20 transition-all flex items-center justify-center gap-1.5">
                    🌐 Apply Translation
                </button>
            </div>
        </div>
    @endif
</div>
