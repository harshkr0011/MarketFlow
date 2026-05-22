<div class="relative z-10 p-6">
    <div class="flex items-center mb-4">
        <div class="bg-navy-700 p-2 rounded-lg mr-3">
            <svg class="w-6 h-6 text-neon-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
        <h3 class="text-lg font-bold text-white font-outfit">AI Visual Canvas (Image Generation)</h3>
    </div>
    <p class="text-gray-400 mb-4 text-sm">Generate beautiful marketing images and ad creatives from text prompts instantly.</p>
    
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Art Style</label>
                <select wire:model="style" class="w-full bg-navy-900/50 border border-navy-700 rounded-lg py-2 px-3 text-white focus:outline-none focus:ring-2 focus:ring-neon-cyan">
                    <option value="Photorealistic">Photorealistic</option>
                    <option value="Cinematic">Cinematic Portrait</option>
                    <option value="3D Render">Modern 3D Glossy</option>
                    <option value="Neon Punk">Cyberpunk Neon</option>
                    <option value="Vector Art">Clean Vector Illustration</option>
                    <option value="Minimalist">Minimalist Studio</option>
                </select>
            </div>
        </div>

        <div class="relative">
            <textarea wire:model="prompt" rows="3" class="w-full bg-navy-900/50 border border-navy-700 rounded-lg py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-neon-cyan focus:border-transparent transition-all" placeholder="E.g. A premium glass water bottle on a bed of ice with cyan backlighting..."></textarea>
            
            <button wire:click="generate" wire:loading.attr="disabled" class="absolute right-2 bottom-3 bg-gradient-to-r from-neon-cyan to-blue-600 hover:from-neon-cyan hover:to-blue-500 text-slate-900 font-bold rounded p-2 transition-all shadow-lg hover:shadow-neon-cyan/20 flex items-center justify-center min-w-[40px]">
                <span wire:loading.remove wire:target="generate">
                    <svg class="w-5 h-5 text-slate-950 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </span>
                <span wire:loading wire:target="generate" class="animate-spin">
                    <svg class="w-5 h-5 text-slate-950" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </span>
            </button>
        </div>
        
        @error('prompt') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <!-- Generated Image Display & Export -->
    @if($generatedImage)
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-navy-900/80 border border-neon-cyan/20 rounded-2xl">
            <!-- Left: Image Preview -->
            <div class="flex items-center justify-center bg-navy-950 rounded-xl overflow-hidden border border-navy-700/60 min-h-[300px] relative">
                <img src="{{ $generatedImage }}" class="object-cover w-full h-full max-h-[350px]" alt="AI Generated Graphic" />
                
                <div class="absolute bottom-3 right-3">
                    <a href="{{ $generatedImage }}" target="_blank" class="bg-navy-900/85 backdrop-blur text-white text-xs px-3 py-1.5 rounded-lg border border-navy-700/60 hover:bg-navy-800 flex items-center gap-1.5 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        Full Size
                    </a>
                </div>
            </div>

            <!-- Right: Export Panel -->
            <div class="flex flex-col justify-between">
                @guest
                    <div class="h-full flex flex-col justify-center items-center text-center p-6 bg-navy-950/45 rounded-xl border border-navy-850">
                        <span class="text-3xl mb-3">🔒</span>
                        <h4 class="text-white font-semibold text-sm font-outfit">Export to Asset Vault</h4>
                        <p class="text-xs text-slate-400 mt-2 mb-6">Create a free account or sign in to save your AI-generated designs directly to your digital asset library.</p>
                        <a href="{{ route('login') }}" class="w-full py-2.5 bg-gradient-to-r from-neon-cyan to-blue-600 hover:from-neon-cyan hover:to-blue-500 text-slate-900 font-bold rounded-xl text-xs transition-all shadow-md hover:scale-[1.02] text-center">
                            Sign In to Export
                        </a>
                    </div>
                @else
                    <div>
                        <h4 class="text-white font-semibold text-base mb-3 font-outfit">Export to Asset Vault</h4>
                        <p class="text-xs text-gray-400 mb-4">Exporting will save this image to the current campaign workspace so it is available in the Asset Vault.</p>
                        
                        <div class="space-y-3">
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Asset Title</label>
                                <input type="text" wire:model="exportTitle" class="w-full bg-navy-900/60 border border-navy-700 rounded-lg py-2 px-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-neon-cyan" placeholder="E.g. Summer Promo Image" />
                                @error('exportTitle') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Category</label>
                                    <select wire:model="exportCategory" class="w-full bg-navy-900/60 border border-navy-700 rounded-lg py-2 px-3 text-xs text-white focus:outline-none focus:ring-1 focus:ring-neon-cyan">
                                        <option value="Social Media">Social Media</option>
                                        <option value="Ad Creative">Ad Creative</option>
                                        <option value="Email Blueprint">Email Blueprint</option>
                                        <option value="Strategy Playbook">Strategy Playbook</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Price Tier</label>
                                    <select wire:model="exportPriceTier" class="w-full bg-navy-900/60 border border-navy-700 rounded-lg py-2 px-3 text-xs text-white focus:outline-none focus:ring-1 focus:ring-neon-cyan">
                                        <option value="Free">Free</option>
                                        <option value="Pro">Pro</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-navy-800">
                        @if($isExported)
                            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs px-3 py-2.5 rounded-lg flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Exported successfully! Check the **Asset Vault** tab.
                            </div>
                        @else
                            <button wire:click="export" class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-bold py-2.5 px-4 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20"></path></svg>
                                Export Asset
                            </button>
                        @endif
                    </div>
                @endguest
            </div>
        </div>
    @endif
</div>
