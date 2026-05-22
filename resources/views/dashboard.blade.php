<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-white leading-tight font-outfit">
                Workspace: <span class="text-neon-cyan">Summer Launch Workspace</span>
            </h2>
            <div class="flex items-center gap-4">
                <span class="px-3 py-1 bg-navy-700 text-slate-300 rounded-full text-xs font-semibold border border-navy-600">
                    Agency Plan: Pro Active
                </span>
            </div>
        </div>
    </x-slot>

    <!-- Main Container -->
    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{
        activeTab: 'analytics',
        currency: 'INR',
        rate: 83,
        formatPrice(val) {
            return this.currency === 'INR' 
                ? '₹' + new Intl.NumberFormat('en-IN').format(val)
                : '$' + new Intl.NumberFormat('en-US').format(Math.round(val / this.rate));
        },
        assetSearch: '',
        assetCategory: 'All',
        assetTier: 'All',
        assets: {{ $assets->toJson() }},
        selectedAsset: null,
        showAssetDrawer: false,
        activeLang: 'en',
        localizedFields: { 
            en: 'Standard Brand Copy: Clean, organic packaging elements, sustainability-first.',
            hi: 'प्राकृतिक पैकेजिंग: पर्यावरण-अनुकूल सामग्री, जैविक स्थिरता प्रथम।',
            es: 'Diseño Sustentable: Enfoque orgánico y ecológico en empaque prémium.' 
        },
        notifications: [],
        showToast(message, type = 'success') {
            this.notifications.push({ id: Date.now(), message, type });
            setTimeout(() => {
                this.notifications = this.notifications.filter(n => n.id !== n.id);
            }, 3000);
        },
        downloadingId: null,
        downloadProgress: 0,
        triggerDownload(asset) {
            this.downloadingId = asset.id;
            this.downloadProgress = 0;
            let interval = setInterval(() => {
                this.downloadProgress += 20;
                if (this.downloadProgress >= 100) {
                    clearInterval(interval);
                    this.downloadingId = null;
                    this.showToast('Downloaded asset: ' + asset.title, 'success');
                }
            }, 200);
        },
        channels: {
            whatsapp: true,
            email: true,
            sms: false
        }
    }">
        <!-- Toast Notifications -->
        <div class="fixed bottom-5 right-5 z-50 space-y-2">
            <template x-for="note in notifications" :key="note.id">
                <div :class="note.type === 'error' ? 'bg-red-500/20 border-red-500 text-red-200' : 'bg-emerald-500/20 border-emerald-500 text-emerald-200'" class="flex items-center gap-2 px-4 py-3 rounded-xl border backdrop-blur-md shadow-lg text-sm">
                    <span x-text="note.type === 'error' ? '❌' : '✅'"></span>
                    <span x-text="note.message"></span>
                </div>
            </template>
        </div>

        <!-- Horizontal Navigation Tabs -->
        <div class="mb-8 bg-navy-800/40 backdrop-blur-xl border border-navy-700/50 rounded-2xl p-2 flex overflow-x-auto md:flex-wrap gap-2 no-scrollbar snap-x">
            <button @click="activeTab = 'analytics'" 
                :class="activeTab === 'analytics' ? 'bg-gradient-to-r from-neon-cyan/20 to-blue-500/10 border-neon-cyan text-white shadow-lg' : 'border-transparent text-slate-400 hover:bg-navy-700/30 hover:text-white'" 
                class="flex items-center px-5 py-3 text-sm font-semibold rounded-xl border-b-2 transition-all duration-200 shrink-0 snap-start">
                <svg class="w-4 h-4 mr-2.5 text-neon-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2"></path></svg>
                Analytics Hub
            </button>
            <button @click="activeTab = 'ai-lab'" 
                :class="activeTab === 'ai-lab' ? 'bg-gradient-to-r from-neon-purple/20 to-indigo-500/10 border-neon-purple text-white shadow-lg' : 'border-transparent text-slate-400 hover:bg-navy-700/30 hover:text-white'" 
                class="flex items-center px-5 py-3 text-sm font-semibold rounded-xl border-b-2 transition-all duration-200 shrink-0 snap-start">
                <svg class="w-4 h-4 mr-2.5 text-neon-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                AI Creative Lab
            </button>
            <button @click="activeTab = 'assets'" 
                :class="activeTab === 'assets' ? 'bg-gradient-to-r from-pink-500/20 to-rose-500/10 border-pink-500 text-white shadow-lg' : 'border-transparent text-slate-400 hover:bg-navy-700/30 hover:text-white'" 
                class="flex items-center px-5 py-3 text-sm font-semibold rounded-xl border-b-2 transition-all duration-200 shrink-0 snap-start">
                <svg class="w-4 h-4 mr-2.5 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2"></path></svg>
                Asset Vault
            </button>
            <button @click="activeTab = 'client-room'" 
                :class="activeTab === 'client-room' ? 'bg-gradient-to-r from-emerald-500/20 to-teal-500/10 border-emerald-500 text-white shadow-lg' : 'border-transparent text-slate-400 hover:bg-navy-700/30 hover:text-white'" 
                class="flex items-center px-5 py-3 text-sm font-semibold rounded-xl border-b-2 transition-all duration-200 shrink-0 snap-start">
                <svg class="w-4 h-4 mr-2.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                Client Room
            </button>
            <button @click="activeTab = 'funnels'" 
                :class="activeTab === 'funnels' ? 'bg-gradient-to-r from-amber-500/20 to-yellow-500/10 border-amber-500 text-white shadow-lg' : 'border-transparent text-slate-400 hover:bg-navy-700/30 hover:text-white'" 
                class="flex items-center px-5 py-3 text-sm font-semibold rounded-xl border-b-2 transition-all duration-200 shrink-0 snap-start">
                <svg class="w-4 h-4 mr-2.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Funnel Engine
            </button>
            <button @click="activeTab = 'partners'" 
                :class="activeTab === 'partners' ? 'bg-gradient-to-r from-indigo-500/20 to-purple-500/10 border-indigo-500 text-white shadow-lg' : 'border-transparent text-slate-400 hover:bg-navy-700/30 hover:text-white'" 
                class="flex items-center px-5 py-3 text-sm font-semibold rounded-xl border-b-2 transition-all duration-200 shrink-0 snap-start">
                <svg class="w-4 h-4 mr-2.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Partner Room
            </button>
            <button @click="activeTab = 'budgets'" 
                :class="activeTab === 'budgets' ? 'bg-gradient-to-r from-emerald-500/20 to-teal-500/10 border-emerald-500 text-white shadow-lg' : 'border-transparent text-slate-400 hover:bg-navy-700/30 hover:text-white'" 
                class="flex items-center px-5 py-3 text-sm font-semibold rounded-xl border-b-2 transition-all duration-200 shrink-0 snap-start">
                <svg class="w-4 h-4 mr-2.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Budget Ledger
            </button>
            <button @click="activeTab = 'shop'" 
                :class="activeTab === 'shop' ? 'bg-gradient-to-r from-neon-cyan/20 to-blue-500/10 border-neon-cyan text-white shadow-lg' : 'border-transparent text-slate-400 hover:bg-navy-700/30 hover:text-white'" 
                class="flex items-center px-5 py-3 text-sm font-semibold rounded-xl border-b-2 transition-all duration-200 shrink-0 snap-start">
                <svg class="w-4 h-4 mr-2.5 text-neon-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                Products Shop
            </button>
            <button @click="activeTab = 'blog'" 
                :class="activeTab === 'blog' ? 'bg-gradient-to-r from-pink-500/20 to-rose-500/10 border-pink-500 text-white shadow-lg' : 'border-transparent text-slate-400 hover:bg-navy-700/30 hover:text-white'" 
                class="flex items-center px-5 py-3 text-sm font-semibold rounded-xl border-b-2 transition-all duration-200 shrink-0 snap-start">
                <svg class="w-4 h-4 mr-2.5 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v12a2 2 0 002 2z"></path></svg>
                CMS Blog
            </button>
            <button @click="activeTab = 'support'" 
                :class="activeTab === 'support' ? 'bg-gradient-to-r from-amber-500/20 to-yellow-500/10 border-amber-500 text-white shadow-lg' : 'border-transparent text-slate-400 hover:bg-navy-700/30 hover:text-white'" 
                class="flex items-center px-5 py-3 text-sm font-semibold rounded-xl border-b-2 transition-all duration-200 shrink-0 snap-start">
                <svg class="w-4 h-4 mr-2.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                AI Support & Tickets
            </button>
            <button @click="activeTab = 'recommendations'" 
                :class="activeTab === 'recommendations' ? 'bg-gradient-to-r from-neon-purple/20 to-indigo-500/10 border-neon-purple text-white shadow-lg' : 'border-transparent text-slate-400 hover:bg-navy-700/30 hover:text-white'" 
                class="flex items-center px-5 py-3 text-sm font-semibold rounded-xl border-b-2 transition-all duration-200 shrink-0 snap-start">
                <svg class="w-4 h-4 mr-2.5 text-neon-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                AI Recommendations
            </button>
        </div>

        <!-- Main Content Area -->
        <div class="w-full">
                
                <!-- TAB 1: ANALYTICS -->
                <div x-show="activeTab === 'analytics'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-white font-outfit">Analytics Hub</h3>
                            <p class="text-sm text-slate-400 font-inter">Live performance tracking and regional metrics.</p>
                        </div>
                        <!-- Currency Toggle -->
                        <div class="bg-navy-800/80 p-1 border border-navy-700 rounded-xl flex">
                            <button @click="currency = 'INR'" :class="currency === 'INR' ? 'bg-neon-cyan text-navy-900 font-bold' : 'text-slate-400 hover:text-white'" class="px-4 py-1.5 rounded-lg text-xs font-semibold transition-all">INR (₹)</button>
                            <button @click="currency = 'USD'" :class="currency === 'USD' ? 'bg-neon-cyan text-navy-900 font-bold' : 'text-slate-400 hover:text-white'" class="px-4 py-1.5 rounded-lg text-xs font-semibold transition-all">USD ($)</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Revenue -->
                        <div class="bg-gradient-to-br from-navy-800 to-navy-900 border border-navy-700 rounded-2xl p-6 shadow-2xl relative overflow-hidden group hover:border-emerald-500/30 transition-all">
                            <div class="absolute -right-12 -top-12 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all"></div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-slate-400 text-xs font-inter uppercase tracking-wider font-semibold">Total Revenue</h4>
                                <div class="bg-emerald-500/10 text-emerald-400 p-2 rounded-lg border border-emerald-500/20">💰</div>
                            </div>
                            <div class="text-3xl font-black text-white font-outfit mb-2" x-text="formatPrice({{ $totalRevenue ?? 1245890 }})"></div>
                            <div class="text-xs text-emerald-400 font-bold">↗ 24.5% <span class="text-slate-500 font-normal">vs last month</span></div>
                        </div>

                        <!-- Active Subscriptions -->
                        <div class="bg-gradient-to-br from-navy-800 to-navy-900 border border-navy-700 rounded-2xl p-6 shadow-2xl relative overflow-hidden group hover:border-indigo-500/30 transition-all flex flex-col justify-between">
                            <div class="absolute -right-12 -bottom-12 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-all"></div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-slate-400 text-xs font-inter uppercase tracking-wider font-semibold">Active Leads</h4>
                                <div class="bg-indigo-500/10 text-indigo-400 p-2 rounded-lg border border-indigo-500/20">👥</div>
                            </div>
                            <div class="text-3xl font-black text-white font-outfit mb-2">1,248</div>
                            <div class="text-xs text-indigo-400 font-bold">↗ 12.5% <span class="text-slate-500 font-normal">this week</span></div>
                        </div>

                        <!-- Conversion Rate -->
                        <div class="bg-gradient-to-br from-navy-800 to-navy-900 border border-navy-700 rounded-2xl p-6 shadow-2xl relative overflow-hidden group hover:border-pink-500/30 transition-all flex flex-col justify-between">
                            <div class="absolute -right-12 -top-12 w-32 h-32 bg-pink-500/10 rounded-full blur-2xl group-hover:bg-pink-500/20 transition-all"></div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-slate-400 text-xs font-inter uppercase tracking-wider font-semibold">Conversion Rate</h4>
                                <div class="bg-pink-500/10 text-pink-400 p-2 rounded-lg border border-pink-500/20">🎯</div>
                            </div>
                            <div class="text-3xl font-black text-white font-outfit mb-2">4.8%</div>
                            <div class="text-xs text-green-400 font-bold">↗ 1.2% <span class="text-slate-500 font-normal">this week</span></div>
                        </div>
                    </div>

                    <!-- Charts & Advanced Metrics Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <!-- ApexCharts Campaign Overview (Col 8) -->
                        <div class="lg:col-span-8 bg-navy-800/60 backdrop-blur-xl border border-navy-700/50 rounded-2xl p-6 shadow-2xl">
                            <h3 class="text-sm font-bold text-white font-outfit mb-4">Regional Lead Flow Trends</h3>
                            <div id="analytics-chart" class="h-64 w-full"></div>
                        </div>

                        <!-- MER & Lead Velocity Panel (Col 4) -->
                        <div class="lg:col-span-4 flex flex-col gap-6">
                            <!-- MER Circular Gauge Card -->
                            <div class="bg-navy-800/60 backdrop-blur-xl border border-navy-700/50 rounded-2xl p-5 shadow-2xl relative overflow-hidden flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-slate-400 text-xs font-inter uppercase tracking-wider font-semibold">Marketing Efficiency Ratio (MER)</h4>
                                        <span class="text-[10px] text-neon-cyan font-bold bg-neon-cyan/10 px-2 py-0.5 rounded border border-neon-cyan/20 font-mono">Live Gauge</span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 mt-1">Multiplier of total revenue vs total ad spend</p>
                                </div>
                                <div class="flex items-center justify-center py-4">
                                    <div class="relative flex items-center justify-center">
                                        <svg class="w-20 h-20 transform -rotate-90">
                                            <circle cx="40" cy="40" r="34" class="stroke-navy-700 fill-transparent" stroke-width="6" />
                                            <circle cx="40" cy="40" r="34" class="stroke-neon-cyan fill-transparent" stroke-width="6" stroke-dasharray="213.5" stroke-dashoffset="53.3" stroke-linecap="round" />
                                        </svg>
                                        <div class="absolute text-center">
                                            <span class="text-xl font-black text-white font-outfit">3.8x</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-[11px] text-slate-400 text-center font-medium">
                                    Ad Spend: <span x-text="formatPrice(327860)"></span> • Revenue multiplier optimal
                                </div>
                            </div>

                            <!-- Lead Velocity Target Card -->
                            <div class="bg-navy-800/60 backdrop-blur-xl border border-navy-700/50 rounded-2xl p-5 shadow-2xl flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-slate-400 text-xs font-inter uppercase tracking-wider font-semibold">Lead Velocity Target</h4>
                                        <span class="text-xs text-neon-purple font-bold">Q2 Target</span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 mt-1">Progress towards campaign acquisition goals</p>
                                </div>
                                <div class="space-y-2 py-3">
                                    <div class="flex justify-between text-xs text-slate-300 font-bold">
                                        <span>1,248 / 2,000 Leads</span>
                                        <span class="text-neon-purple font-mono">62.4%</span>
                                    </div>
                                    <div class="w-full bg-navy-950 rounded-full h-2 overflow-hidden border border-navy-700">
                                        <div class="bg-gradient-to-r from-neon-purple to-indigo-500 h-full rounded-full" style="width: 62.4%"></div>
                                    </div>
                                </div>
                                <div class="text-[10px] text-slate-400 flex items-center justify-between font-mono">
                                    <span>Pace: +14 leads/day</span>
                                    <span class="text-emerald-400 font-bold">On Schedule</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- E-Portal ROI & Attribution Analytics -->
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                        <!-- Multi-channel Spend & Conversion Breakdown -->
                        <div class="xl:col-span-2 bg-navy-800/60 backdrop-blur-xl border border-navy-700/50 rounded-2xl p-6 shadow-2xl space-y-4">
                            <div class="flex justify-between items-center border-b border-navy-700 pb-3 mb-2">
                                <h3 class="text-sm font-bold text-white font-outfit flex items-center gap-2">
                                    <span>📊</span> Multi-Channel Attribution & Spend Efficiency
                                </h3>
                                <span class="text-[10px] text-neon-cyan font-bold bg-neon-cyan/10 px-2 py-0.5 rounded border border-neon-cyan/20 font-mono">Last 30 Days</span>
                            </div>
                            
                            <div class="space-y-4">
                                <!-- Meta Ads -->
                                <div class="space-y-1.5">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-white font-bold">Meta Ads (Facebook & Instagram)</span>
                                        <span class="text-slate-400"><span class="font-bold text-slate-200">ROI: 4.2x</span> • Cost: <span x-text="formatPrice(120000)"></span></span>
                                    </div>
                                    <div class="w-full bg-navy-950 rounded-full h-2 overflow-hidden border border-navy-700">
                                        <div class="bg-indigo-500 h-full rounded-full" style="width: 84%"></div>
                                    </div>
                                </div>

                                <!-- Google Search Ads -->
                                <div class="space-y-1.5">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-white font-bold">Google Ads (Search & Display)</span>
                                        <span class="text-slate-400"><span class="font-bold text-slate-200">ROI: 3.6x</span> • Cost: <span x-text="formatPrice(98000)"></span></span>
                                    </div>
                                    <div class="w-full bg-navy-950 rounded-full h-2 overflow-hidden border border-navy-700">
                                        <div class="bg-neon-purple h-full rounded-full" style="width: 72%"></div>
                                    </div>
                                </div>

                                <!-- YouTube Video Ads -->
                                <div class="space-y-1.5">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-white font-bold">YouTube Ads (Video Campaigns)</span>
                                        <span class="text-slate-400"><span class="font-bold text-slate-200">ROI: 5.1x</span> • Cost: <span x-text="formatPrice(65000)"></span></span>
                                    </div>
                                    <div class="w-full bg-navy-950 rounded-full h-2 overflow-hidden border border-navy-700">
                                        <div class="bg-red-500 h-full rounded-full" style="width: 90%"></div>
                                    </div>
                                </div>

                                <!-- LinkedIn Professional Ads -->
                                <div class="space-y-1.5">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-white font-bold">LinkedIn Ads (SaaS Lead Gen)</span>
                                        <span class="text-slate-400"><span class="font-bold text-slate-200">ROI: 2.8x</span> • Cost: <span x-text="formatPrice(44860)"></span></span>
                                    </div>
                                    <div class="w-full bg-navy-950 rounded-full h-2 overflow-hidden border border-navy-700">
                                        <div class="bg-blue-600 h-full rounded-full" style="width: 56%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Executive Reporting Exporter -->
                        <div class="xl:col-span-1 bg-navy-800/60 backdrop-blur-xl border border-navy-700/50 rounded-2xl p-6 shadow-2xl flex flex-col justify-between"
                             x-data="{ exporting: false, progress: 0 }">
                            <div>
                                <div class="flex justify-between items-center border-b border-navy-700 pb-3 mb-4">
                                    <h3 class="text-sm font-bold text-white font-outfit flex items-center gap-2">
                                        <span>📂</span> Executive Reports
                                    </h3>
                                    <span class="text-[9px] bg-emerald-500/10 text-emerald-400 px-2 py-0.5 rounded font-mono font-bold uppercase">Ready</span>
                                </div>
                                <p class="text-xs text-slate-400 leading-relaxed mb-4">
                                    Generate and download a comprehensive board-ready PDF attribution deck covering CAC trends, multi-channel spend efficiency ratios, and regional ROI.
                                </p>

                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between text-[11px] text-slate-300 font-medium">
                                        <span>CAC Average (This Month):</span>
                                        <strong class="text-white font-mono" x-text="formatPrice(262)"></strong>
                                    </div>
                                    <div class="flex justify-between text-[11px] text-slate-300 font-medium">
                                        <span>Total Blended ROI:</span>
                                        <strong class="text-emerald-400 font-mono">4.12x</strong>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <template x-if="exporting">
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-xs text-neon-cyan font-mono">
                                            <span>Compiling metrics...</span>
                                            <span x-text="progress + '%'"></span>
                                        </div>
                                        <div class="w-full bg-navy-950 h-2 rounded-full overflow-hidden border border-navy-700">
                                            <div class="bg-neon-cyan h-full transition-all duration-200" :style="'width: ' + progress + '%'"></div>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="!exporting">
                                    <button @click="exporting = true; progress = 0; 
                                        let interval = setInterval(() => { 
                                            progress += 10; 
                                            if(progress >= 100) { 
                                                clearInterval(interval); 
                                                setTimeout(() => { exporting = false; showToast('Executive ROI Report downloaded successfully!', 'success'); }, 500); 
                                            } 
                                        }, 150)" 
                                        class="w-full py-2.5 bg-gradient-to-r from-neon-cyan to-blue-600 hover:from-neon-cyan hover:to-blue-500 text-navy-950 font-bold text-xs rounded-xl transition-all shadow-lg hover:shadow-neon-cyan/25 flex items-center justify-center gap-1.5">
                                        📥 Export Executive ROI PDF
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Live Activity Feed & Regional Lead Feed -->
                    <div class="bg-navy-800/60 backdrop-blur-xl border border-navy-700/50 rounded-2xl p-6 shadow-2xl">
                        <h3 class="text-sm font-bold text-white font-outfit mb-4 flex items-center gap-2">
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            Live Activity Feed & Lead Milestones
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left: Lead Acquisition Events -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Recent Acquisitions</h4>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between p-3 bg-navy-950/60 rounded-xl border border-navy-700/50 hover:border-neon-cyan/20 transition-all">
                                        <div class="flex items-center gap-3">
                                            <span class="text-lg">🇮🇳</span>
                                            <div>
                                                <div class="text-xs font-bold text-white">Ananya Sharma</div>
                                                <div class="text-[10px] text-slate-400">Delhi • Zero-Waste Campaign</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs text-neon-cyan font-bold font-mono">+1 lead</span>
                                            <div class="text-[9px] text-slate-500 font-mono">2 mins ago</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-navy-950/60 rounded-xl border border-navy-700/50 hover:border-neon-cyan/20 transition-all">
                                        <div class="flex items-center gap-3">
                                            <span class="text-lg">🇮🇳</span>
                                            <div>
                                                <div class="text-xs font-bold text-white">Rohan Das</div>
                                                <div class="text-[10px] text-slate-400">Mumbai • Fashion Launch</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs text-neon-cyan font-bold font-mono">+1 lead</span>
                                            <div class="text-[9px] text-slate-500 font-mono">12 mins ago</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-navy-950/60 rounded-xl border border-navy-700/50 hover:border-neon-cyan/20 transition-all">
                                        <div class="flex items-center gap-3">
                                            <span class="text-lg">🇮🇳</span>
                                            <div>
                                                <div class="text-xs font-bold text-white">Priya Patel</div>
                                                <div class="text-[10px] text-slate-400">Bangalore • Tech SaaS Beta</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs text-neon-cyan font-bold font-mono">+1 lead</span>
                                            <div class="text-[9px] text-slate-500 font-mono">45 mins ago</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Platform & Campaign Logs -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Campaign Log & Milestones</h4>
                                <div class="space-y-2 font-mono text-[11px] text-slate-300">
                                    <div class="p-3 bg-navy-950/40 rounded-xl border border-navy-700/50 flex items-start gap-2.5">
                                        <span class="text-emerald-400">✔</span>
                                        <div>
                                            <div class="font-bold text-white">Campaign Approved for Launch</div>
                                            <div class="text-slate-500 mt-0.5">Approved by client Summer Launch Workspace.</div>
                                        </div>
                                    </div>
                                    <div class="p-3 bg-navy-950/40 rounded-xl border border-navy-700/50 flex items-start gap-2.5">
                                        <span class="text-indigo-400">ℹ</span>
                                        <div>
                                            <div class="font-bold text-white">AI Visual Asset Seeded</div>
                                            <div class="text-slate-500 mt-0.5">Asset 'AI Ad: zero-waste bamboo' exported to vault by agency admin.</div>
                                        </div>
                                    </div>
                                    <div class="p-3 bg-navy-950/40 rounded-xl border border-navy-700/50 flex items-start gap-2.5">
                                        <span class="text-amber-400">⚡</span>
                                        <div>
                                            <div class="font-bold text-white">Meta Ads Pixel Triggered</div>
                                            <div class="text-slate-500 mt-0.5">Acquisition API dispatched leads update for Mumbai.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: AI CREATIVE LAB -->
                <div x-show="activeTab === 'ai-lab'" x-data="{ aiTab: 'copywriter' }" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <!-- Sub-tabs for AI Lab -->
                    <div class="flex border-b border-navy-700/60 gap-4 mb-2">
                        <button @click="aiTab = 'copywriter'" :class="aiTab === 'copywriter' ? 'border-b-2 border-neon-purple text-white' : 'text-slate-400 hover:text-white'" class="pb-3 px-4 text-xs font-bold transition-all flex items-center gap-1">
                            ✍ AI Copywriter
                        </button>
                        <button @click="aiTab = 'visual-canvas'" :class="aiTab === 'visual-canvas' ? 'border-b-2 border-neon-cyan text-white' : 'text-slate-400 hover:text-white'" class="pb-3 px-4 text-xs font-bold transition-all flex items-center gap-1">
                            🎨 AI Visual Canvas
                        </button>
                    </div>

                    <div x-show="aiTab === 'copywriter'" class="bg-navy-800/60 backdrop-blur-xl border border-navy-700/50 overflow-hidden shadow-2xl rounded-2xl">
                        <livewire:ai-copywriter />
                    </div>

                    <div x-show="aiTab === 'visual-canvas'" class="bg-navy-800/60 backdrop-blur-xl border border-navy-700/50 overflow-hidden shadow-2xl rounded-2xl" style="display: none;">
                        <livewire:ai-visual-canvas />
                    </div>
                </div>

                <!-- TAB 3: ASSET VAULT -->
                <div x-show="activeTab === 'assets'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-white font-outfit">Asset Vault</h3>
                            <p class="text-sm text-slate-400 font-inter">Global Marketing Asset Library.</p>
                        </div>
                        
                        <!-- Search Bar -->
                        <div class="relative w-full sm:w-64">
                            <input x-model="assetSearch" type="text" placeholder="Search templates... (⌘ K)" class="w-full bg-navy-800 border border-navy-700 rounded-xl py-2 pl-10 pr-4 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-neon-purple focus:border-transparent transition-all">
                            <div class="absolute left-3 top-2.5 text-slate-500">🔍</div>
                        </div>
                    </div>

                    <!-- Filters bar -->
                    <div class="flex flex-wrap gap-3 items-center justify-between border-b border-navy-800 pb-4">
                        <!-- Category Pills -->
                        <div class="flex flex-wrap gap-2">
                            <button @click="assetCategory = 'All'" :class="assetCategory === 'All' ? 'bg-neon-purple text-white' : 'bg-navy-800 text-slate-400 hover:text-white'" class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all">All Categories</button>
                            <button @click="assetCategory = 'Social Media'" :class="assetCategory === 'Social Media' ? 'bg-neon-purple text-white' : 'bg-navy-800 text-slate-400 hover:text-white'" class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all">Social Media</button>
                            <button @click="assetCategory = 'Email'" :class="assetCategory === 'Email' ? 'bg-neon-purple text-white' : 'bg-navy-800 text-slate-400 hover:text-white'" class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all">Emails</button>
                            <button @click="assetCategory = 'Playbook'" :class="assetCategory === 'Playbook' ? 'bg-neon-purple text-white' : 'bg-navy-800 text-slate-400 hover:text-white'" class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all">Playbooks</button>
                        </div>

                        <!-- Price Tier Filter -->
                        <div class="flex gap-2">
                            <button @click="assetTier = 'All'" :class="assetTier === 'All' ? 'border-neon-cyan text-neon-cyan' : 'border-navy-700 text-slate-400'" class="px-3 py-1 border rounded-lg text-xs font-semibold transition-all">All Tiers</button>
                            <button @click="assetTier = 'Free'" :class="assetTier === 'Free' ? 'border-neon-cyan text-neon-cyan' : 'border-navy-700 text-slate-400'" class="px-3 py-1 border rounded-lg text-xs font-semibold transition-all">Free</button>
                            <button @click="assetTier = 'Pro'" :class="assetTier === 'Pro' ? 'border-neon-cyan text-neon-cyan' : 'border-navy-700 text-slate-400'" class="px-3 py-1 border rounded-lg text-xs font-semibold transition-all">Pro</button>
                        </div>
                    </div>

                    <!-- Assets Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <template x-for="item in assets.filter(i => (assetCategory === 'All' || i.category === assetCategory) && (assetTier === 'All' || i.price_tier === assetTier) && (i.title.toLowerCase().includes(assetSearch.toLowerCase())))" :key="item.id">
                            <div class="bg-navy-800/60 border border-navy-700/50 rounded-2xl overflow-hidden shadow-2xl transition-all duration-300 hover:border-neon-purple/40 hover:-translate-y-1 group">
                                <div class="h-40 w-full overflow-hidden bg-navy-900 relative">
                                    <img :src="item.thumbnail_path" class="w-full h-full object-cover opacity-80 group-hover:scale-105 transition-transform duration-500" alt="Asset Thumbnail">
                                    <div class="absolute top-3 right-3">
                                        <span :class="item.price_tier === 'Pro' ? 'bg-neon-purple border-neon-purple text-white shadow-neon-purple/20' : 'bg-emerald-500/20 border-emerald-500/30 text-emerald-400'" class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded-full border shadow-sm font-mono" x-text="item.price_tier"></span>
                                    </div>
                                </div>
                                <div class="p-5 space-y-4">
                                    <div>
                                        <span class="px-2 py-0.5 bg-navy-700 text-slate-300 rounded text-[10px] font-bold font-mono" x-text="item.type"></span>
                                        <h4 class="text-white font-bold text-base mt-2 line-clamp-1" x-text="item.title"></h4>
                                    </div>
                                    <div class="flex justify-between items-center border-t border-navy-700/50 pt-4">
                                        <span class="text-xs text-slate-400" x-text="item.category"></span>
                                        
                                        <!-- Download Button & Progress Bar -->
                                        <div>
                                            <template x-if="downloadingId === item.id">
                                                <div class="flex flex-col items-end gap-1 w-24">
                                                    <span class="text-[10px] text-neon-cyan font-mono" x-text="downloadProgress + '%'"></span>
                                                    <div class="w-full h-1.5 bg-navy-700 rounded-full overflow-hidden">
                                                        <div class="bg-neon-cyan h-full transition-all duration-200" :style="'width: ' + downloadProgress + '%'"></div>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="downloadingId !== item.id">
                                                <div class="flex items-center gap-2">
                                                     <button @click="selectedAsset = item; showAssetDrawer = true;" class="px-2.5 py-1.5 bg-navy-700/80 hover:bg-navy-700 text-slate-300 text-xs font-semibold rounded-lg transition-all border border-navy-600/50 flex items-center gap-1">
                                                         🛡️ Compliance
                                                     </button>
                                                     <button @click="triggerDownload(item)" class="px-3.5 py-1.5 bg-gradient-to-r from-neon-purple to-indigo-600 hover:from-neon-purple hover:to-indigo-500 text-white text-xs font-semibold rounded-lg transition-all shadow-lg hover:shadow-neon-purple/15 flex items-center gap-1">
                                                         📥 Download
                                                     </button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- TAB 4: CLIENT ROOM -->
                <div x-show="activeTab === 'client-room'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                     <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                         <!-- Left: Livewire Chat Collaboration -->
                         <div class="xl:col-span-2 bg-navy-800/60 backdrop-blur-xl border border-navy-700/50 overflow-hidden shadow-2xl rounded-2xl">
                             @php
                                 $workspaceId = \App\Models\Workspace::first()->id ?? null;
                             @endphp
                             <livewire:collaboration-chat :workspaceId="$workspaceId" />
                         </div>

                         <!-- Right: Campaign Calendar & Milestones -->
                         <div class="xl:col-span-1 bg-navy-800/60 backdrop-blur-xl border border-navy-700/50 p-6 shadow-2xl rounded-2xl flex flex-col justify-between">
                             <div>
                                 <div class="flex items-center justify-between border-b border-navy-700 pb-3 mb-4">
                                     <h4 class="text-white font-bold text-base flex items-center gap-2">
                                         <span>📅</span> Campaign Calendar
                                     </h4>
                                     <span class="text-xs text-neon-purple font-mono uppercase font-bold">May 2026</span>
                                 </div>

                                 <!-- Mini Calendar Grid -->
                                 <div class="grid grid-cols-7 gap-1 text-center text-[10px] text-slate-500 font-mono mb-4">
                                     <span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span><span>S</span>
                                     
                                     <!-- Dummy days to align calendar starting day (Friday) -->
                                     <span class="text-slate-700"></span><span class="text-slate-700"></span><span class="text-slate-700"></span><span class="text-slate-700"></span>
                                     
                                     <template x-for="day in Array.from({length: 31}, (_, i) => i + 1)" :key="day">
                                         <div :class="[
                                             day === 19 ? 'bg-neon-purple text-white font-bold shadow-lg shadow-neon-purple/20' : '',
                                             [5, 12, 26].includes(day) ? 'bg-navy-700 text-slate-200 border border-navy-600/40' : '',
                                             'aspect-square flex items-center justify-center rounded-lg cursor-pointer hover:bg-navy-700/50 transition'
                                         ]" class="p-1 text-slate-400">
                                             <span x-text="day"></span>
                                         </div>
                                     </template>
                                 </div>

                                 <!-- Key Milestones & Approval Statuses -->
                                 <h5 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Workflow Approvals</h5>
                                 <div class="space-y-3">
                                     <!-- Milestone 1 -->
                                     <div class="p-3 bg-navy-950/40 border border-navy-700/40 rounded-xl flex items-start gap-2.5 hover:border-amber-500/30 transition duration-200">
                                         <span class="w-2 h-2 rounded-full bg-amber-500 mt-1 animate-pulse"></span>
                                         <div class="flex-1">
                                             <div class="flex items-center justify-between">
                                                 <h6 class="text-xs font-bold text-white">Social Campaign Draft</h6>
                                                 <span class="text-[9px] text-amber-400 font-bold uppercase font-mono">In Review</span>
                                             </div>
                                             <p class="text-[10px] text-slate-500 mt-0.5">Instagram Carousel Post pack assets pending client sign-off.</p>
                                         </div>
                                     </div>

                                     <!-- Milestone 2 -->
                                     <div class="p-3 bg-navy-950/40 border border-navy-700/40 rounded-xl flex items-start gap-2.5 hover:border-emerald-500/30 transition duration-200">
                                         <span class="w-2 h-2 rounded-full bg-emerald-500 mt-1"></span>
                                         <div class="flex-1">
                                             <div class="flex items-center justify-between">
                                                 <h6 class="text-xs font-bold text-white">SaaS Newsletter HTML</h6>
                                                 <span class="text-[9px] text-emerald-400 font-bold uppercase font-mono">Approved</span>
                                             </div>
                                             <p class="text-[10px] text-slate-500 mt-0.5">Approved by client. Ready for ESP scheduled dispatch on May 22.</p>
                                         </div>
                                     </div>

                                     <!-- Milestone 3 -->
                                     <div class="p-3 bg-navy-950/40 border border-navy-700/40 rounded-xl flex items-start gap-2.5 opacity-60 hover:opacity-100 transition duration-200">
                                         <span class="w-2 h-2 rounded-full bg-indigo-500 mt-1"></span>
                                         <div class="flex-1">
                                             <div class="flex items-center justify-between">
                                                 <h6 class="text-xs font-bold text-white">Print Brochure RFP Dispatch</h6>
                                                 <span class="text-[9px] text-indigo-400 font-bold uppercase font-mono">Scheduled</span>
                                             </div>
                                             <p class="text-[10px] text-slate-500 mt-0.5">Tender RFP bids close on May 26.</p>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             
                             <div class="bg-navy-900/40 border border-navy-700/50 p-3 rounded-xl mt-4 text-[10px] text-slate-400 leading-relaxed">
                                 💡 **Pro Tip:** In-context visual annotations can be dropped directly on the layout screen to request modifications.
                             </div>
                         </div>
                     </div>
                 </div>

                <!-- TAB 5: FUNNEL ENGINE -->
                <div x-show="activeTab === 'funnels'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-white font-outfit">Funnel Engine</h3>
                            <p class="text-sm text-slate-400 font-inter">Visual drip automated funnel & ecosystem integrations.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Integrations Toggles -->
                        <div class="lg:col-span-1 bg-navy-800/60 border border-navy-700/50 rounded-2xl p-6 shadow-2xl space-y-6">
                            <h4 class="text-white font-bold text-base border-b border-navy-700 pb-3 flex items-center gap-2">
                                <span>🔌</span> Active Connectors
                            </h4>
                            
                            <!-- WhatsApp Connector -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-xl">💬</div>
                                    <div>
                                        <h5 class="text-sm font-bold text-white">WhatsApp Business API</h5>
                                        <p class="text-[11px] text-emerald-400">92.4% Delivery rate</p>
                                    </div>
                                </div>
                                <button @click="channels.whatsapp = !channels.whatsapp" :class="channels.whatsapp ? 'bg-emerald-500 text-white' : 'bg-navy-900 border-navy-700 text-slate-500'" class="w-12 h-6 rounded-full p-1 transition-colors flex items-center" :class="channels.whatsapp ? 'justify-end' : 'justify-start'">
                                    <div class="w-4 h-4 rounded-full bg-white shadow-md"></div>
                                </button>
                            </div>

                            <!-- Email Connector -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-neon-purple/10 border border-neon-purple/30 flex items-center justify-center text-xl">✉️</div>
                                    <div>
                                        <h5 class="text-sm font-bold text-white">Email (AWS SES)</h5>
                                        <p class="text-[11px] text-neon-purple">99.8% Inbox rate</p>
                                    </div>
                                </div>
                                <button @click="channels.email = !channels.email" :class="channels.email ? 'bg-emerald-500 text-white' : 'bg-navy-900 border-navy-700 text-slate-500'" class="w-12 h-6 rounded-full p-1 transition-colors flex items-center" :class="channels.email ? 'justify-end' : 'justify-start'">
                                    <div class="w-4 h-4 rounded-full bg-white shadow-md"></div>
                                </button>
                            </div>

                            <!-- SMS Connector -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-neon-cyan/10 border border-neon-cyan/30 flex items-center justify-center text-xl">📱</div>
                                    <div>
                                        <h5 class="text-sm font-bold text-white">SMS Gateway (Twilio)</h5>
                                        <p class="text-[11px] text-slate-500">Disabled</p>
                                    </div>
                                </div>
                                <button @click="channels.sms = !channels.sms" :class="channels.sms ? 'bg-emerald-500 text-white' : 'bg-navy-900 border-navy-700 text-slate-500'" class="w-12 h-6 rounded-full p-1 transition-colors flex items-center" :class="channels.sms ? 'justify-end' : 'justify-start'">
                                    <div class="w-4 h-4 rounded-full bg-white shadow-md"></div>
                                </button>
                            </div>
                        </div>

                        <!-- Funnel Diagram (Visual Sandbox) -->
                        <div class="lg:col-span-2 bg-navy-800/60 border border-navy-700/50 rounded-2xl p-6 shadow-2xl flex flex-col justify-between">
                            <h4 class="text-white font-bold text-base border-b border-navy-700 pb-3 flex items-center gap-2">
                                <span>🛠️</span> Active Funnel Sequence
                            </h4>

                            <!-- Diagram Nodes -->
                            <div class="flex flex-col md:flex-row items-center justify-between gap-4 py-8 relative">
                                <!-- Step 1 -->
                                <div class="z-10 bg-navy-900 border border-navy-700 p-4 rounded-2xl w-44 text-center">
                                    <div class="text-xs text-neon-cyan font-bold font-mono">STEP 1: TRIGGER</div>
                                    <div class="text-sm font-bold text-white mt-1">Lead Acquired</div>
                                    <div class="text-[10px] text-slate-400 mt-1">Facebook Lead Form</div>
                                </div>

                                <!-- Arrow -->
                                <div class="hidden md:block text-slate-600 text-2xl font-bold animate-pulse">➔</div>

                                <!-- Step 2 -->
                                <div class="z-10 border p-4 rounded-2xl w-44 text-center" :class="channels.whatsapp ? 'bg-emerald-500/10 border-emerald-500/30' : 'bg-navy-900 border-navy-700 opacity-50'">
                                    <div class="text-xs font-bold font-mono" :class="channels.whatsapp ? 'text-emerald-400' : 'text-slate-500'">STEP 2: ACTION</div>
                                    <div class="text-sm font-bold text-white mt-1">WhatsApp Greeting</div>
                                    <div class="text-[10px] text-slate-400 mt-1" x-text="channels.whatsapp ? 'Active (Auto-Responder)' : 'Disabled'"></div>
                                </div>

                                <!-- Arrow -->
                                <div class="hidden md:block text-slate-600 text-2xl font-bold animate-pulse">➔</div>

                                <!-- Step 3 -->
                                <div class="z-10 border p-4 rounded-2xl w-44 text-center" :class="channels.email ? 'bg-neon-purple/10 border-neon-purple/30' : 'bg-navy-900 border-navy-700 opacity-50'">
                                    <div class="text-xs font-bold font-mono" :class="channels.email ? 'text-neon-purple' : 'text-slate-500'">STEP 3: DRIP</div>
                                    <div class="text-sm font-bold text-white mt-1">Email Series</div>
                                    <div class="text-[10px] text-slate-400 mt-1" x-text="channels.email ? 'Active (Nurture Sequence)' : 'Disabled'"></div>
                                </div>
                            </div>

                            <div class="bg-navy-900/60 p-4 border border-navy-700 rounded-xl text-xs text-slate-400 leading-relaxed">
                                💡 **Funnel Engine Tip:** Connect other connectors like Zapier, Make, or Webhooks in the Settings tab to map external custom CRM parameters directly into your workspaces.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 6: PARTNER ROOM -->
                <div x-show="activeTab === 'partners'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-white font-outfit">Partner RFP & Bidding Room</h3>
                            <p class="text-sm text-slate-400 font-inter">Secure agency collaborations, print houses, and dispatch RFP tenders.</p>
                        </div>
                    </div>
                    <livewire:partner-room />
                </div>

                <!-- TAB 7: BUDGET LEDGER -->
                <div x-show="activeTab === 'budgets'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-white font-outfit">Budget Ledger & Drawdowns</h3>
                            <p class="text-sm text-slate-400 font-inter">Enforce spending limits, view active balances, and request campaign drawdowns.</p>
                        </div>
                    </div>
                    <livewire:budget-ledger />
                </div>

                <!-- TAB 8: PRODUCTS SHOP -->
                <div x-show="activeTab === 'shop'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-white font-outfit">Products & Shop Catalog</h3>
                            <p class="text-sm text-slate-400 font-inter">Purchase campaign templates, assets, or consulting sessions with mock Stripe checkouts.</p>
                        </div>
                    </div>
                    <livewire:product-shop />
                </div>

                <!-- TAB 9: CMS BLOG -->
                <div x-show="activeTab === 'blog'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <livewire:blog-catalog />
                </div>

                <!-- TAB 10: AI SUPPORT & TICKETS -->
                <div x-show="activeTab === 'support'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-white font-outfit">Support & Customer Ticketing</h3>
                            <p class="text-sm text-slate-400 font-inter">Submit support tickets to team members or chat with our automated Gemini support bot.</p>
                        </div>
                    </div>
                    <livewire:support-center />
                </div>

                <!-- TAB 11: SMART RECOMMENDATIONS -->
                <div x-show="activeTab === 'recommendations'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-white font-outfit">AI-Powered Trend Predictions</h3>
                            <p class="text-sm text-slate-400 font-inter">Personalized marketing advice, campaign predictions, and suggested shop items.</p>
                        </div>
                    </div>
                    <livewire:smart-recommendations />
                </div>

            </div>
        </div>

        <!-- Brand Compliance & Asset Versioning Side Drawer -->
        <div x-show="showAssetDrawer" 
             class="fixed inset-0 z-50 overflow-hidden" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm" @click="showAssetDrawer = false"></div>

            <!-- Drawer Container -->
            <div class="absolute inset-y-0 right-0 max-w-full flex pl-10">
                <div class="w-screen max-w-md bg-navy-900 border-l border-navy-850 shadow-2xl flex flex-col"
                     x-show="showAssetDrawer"
                     x-transition:enter="transform transition ease-out duration-300 sm:duration-500"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in duration-300 sm:duration-500"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full">
                    
                    <!-- Header -->
                    <div class="px-6 py-5 bg-navy-950/50 border-b border-navy-800 flex items-center justify-between">
                        <h2 class="text-base font-bold text-white font-outfit flex items-center gap-2">
                            <span>🛡️</span> Compliance & Licensing Control
                        </h2>
                        <button @click="showAssetDrawer = false" class="text-slate-400 hover:text-white transition">
                            ✕
                        </button>
                    </div>

                    <!-- Drawer Content -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-6">
                        <template x-if="selectedAsset">
                            <div class="space-y-6">
                                <!-- Asset Thumbnail & Title Card -->
                                <div class="bg-navy-950/40 border border-navy-800 rounded-xl overflow-hidden p-4 flex gap-4">
                                    <img :src="selectedAsset.thumbnail_path" class="w-16 h-16 object-cover rounded-lg border border-navy-700" />
                                    <div class="flex-1">
                                        <h3 class="text-sm font-bold text-white line-clamp-2" x-text="selectedAsset.title"></h3>
                                        <div class="flex gap-2 mt-2">
                                            <span class="px-2 py-0.5 bg-navy-800 text-slate-300 text-[10px] rounded font-mono font-bold" x-text="selectedAsset.type"></span>
                                            <span class="px-2 py-0.5 bg-neon-purple/10 text-neon-purple text-[10px] rounded font-semibold border border-neon-purple/20" x-text="'v' + (selectedAsset.version_major ?? 1) + '.' + (selectedAsset.version_minor ?? 0) + '.0'"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Brand Compliance Verification Checklist -->
                                <div class="space-y-3">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Brand Compliance Checklist</h4>
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between p-3 bg-navy-950/60 rounded-xl border border-navy-800">
                                            <div class="flex items-center gap-2">
                                                <span class="text-emerald-400 font-bold">✔</span>
                                                <span class="text-xs text-slate-200">Colors (Brand HSL Hex palette match)</span>
                                            </div>
                                            <span class="text-[10px] text-emerald-400 font-bold uppercase font-mono">Passed</span>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-navy-950/60 rounded-xl border border-navy-800">
                                            <div class="flex items-center gap-2">
                                                <span class="text-emerald-400 font-bold">✔</span>
                                                <span class="text-xs text-slate-200">Typography (Outfit / Inter fallback)</span>
                                            </div>
                                            <span class="text-[10px] text-emerald-400 font-bold uppercase font-mono">Passed</span>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-navy-950/60 rounded-xl border border-navy-800">
                                            <div class="flex items-center gap-2">
                                                <span class="text-emerald-400 font-bold">✔</span>
                                                <span class="text-xs text-slate-200">Clear Space Margins & Alignment</span>
                                            </div>
                                            <span class="text-[10px] text-emerald-400 font-bold uppercase font-mono">Passed</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Version History -->
                                <div class="space-y-3">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Version History Control</h4>
                                    <div class="space-y-2 font-mono text-[10px] text-slate-400">
                                        <div class="p-3 bg-emerald-500/5 border border-emerald-500/20 rounded-xl flex justify-between items-center">
                                            <div>
                                                <strong class="text-white">v1.1.0</strong>
                                                <span class="block text-slate-500 text-[9px] mt-0.5">Customized Fields Seeded</span>
                                            </div>
                                            <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 font-bold rounded">Active & Current</span>
                                        </div>
                                        <div class="p-3 bg-navy-950/40 border border-navy-800 rounded-xl flex justify-between items-center opacity-60">
                                            <div>
                                                <strong>v1.0.0</strong>
                                                <span class="block text-slate-500 text-[9px] mt-0.5">Initial template upload</span>
                                            </div>
                                            <span class="px-2 py-0.5 bg-navy-800 text-slate-500 rounded font-bold">Archived</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Licensing and Rights Parameters -->
                                <div class="space-y-3">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Licensing & Territory Controls</h4>
                                    <div class="p-4 bg-navy-950/60 border border-navy-800 rounded-xl space-y-3 text-xs text-slate-300">
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Distribution:</span>
                                            <span class="font-bold text-slate-200" x-text="selectedAsset.territory_restriction ?? 'Global (All Regions)'"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Expiration Date:</span>
                                            <span class="font-bold text-slate-200" x-text="selectedAsset.expires_at ? new Date(selectedAsset.expires_at).toLocaleDateString() : 'No Expiry'"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Localization Previewer -->
                                <div class="space-y-3">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Regional Asset Localization</h4>
                                    <div class="p-4 bg-navy-950/60 border border-navy-800 rounded-xl space-y-4">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Target Market Language</label>
                                            <select x-model="activeLang" class="w-full bg-navy-900 border border-navy-700 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-neon-purple">
                                                <option value="en">English (Default)</option>
                                                <option value="hi">Hindi (India Regional)</option>
                                                <option value="es">Spanish (North America / Global)</option>
                                            </select>
                                        </div>
                                        <div class="p-3 bg-navy-900/60 border border-navy-800 rounded-lg">
                                            <span class="block text-[9px] font-mono text-neon-purple uppercase tracking-wider mb-1">Localized Compliance Copy</span>
                                            <p class="text-xs text-slate-200 leading-relaxed italic" x-text="localizedFields[activeLang]"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Footer Action -->
                    <div class="p-6 bg-navy-950/50 border-t border-navy-800 flex justify-end gap-3">
                        <button @click="showAssetDrawer = false" class="px-4 py-2 bg-navy-800 hover:bg-navy-700 text-slate-300 rounded-lg text-xs font-semibold transition">
                            Close
                        </button>
                        <button @click="triggerDownload(selectedAsset); showAssetDrawer = false;" class="px-4 py-2 bg-gradient-to-r from-neon-purple to-indigo-600 hover:from-neon-purple hover:to-indigo-500 text-white rounded-lg text-xs font-semibold transition">
                            Download compliance.zip
                        </button>
                    </div>
                </div>
            </div>
        </div>

    <!-- ApexCharts Initialization script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Number Formatting for INR
            const formatCurrency = (number, curr) => {
                return new Intl.NumberFormat(curr === 'INR' ? 'en-IN' : 'en-US', {
                    style: 'currency',
                    currency: curr,
                    maximumFractionDigits: 0
                }).format(number);
            };

            var options = {
                chart: { 
                    type: 'area', 
                    height: 260, 
                    toolbar: { show: false }, 
                    fontFamily: 'Inter, sans-serif',
                    background: 'transparent'
                },
                colors: ['#06b6d4'], // Cyan
                series: [{ name: 'Leads', data: [320, 480, 240, 520, 680, 1020, 1248] }],
                xaxis: { 
                    categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], 
                    labels: { style: { colors: '#94a3b8' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: { 
                    labels: { 
                        formatter: (val) => val, 
                        style: { colors: '#94a3b8' } 
                    } 
                },
                grid: {
                    borderColor: 'rgba(255,255,255,0.05)',
                    strokeDashArray: 4,
                },
                stroke: { curve: 'smooth', width: 2 },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] } },
                dataLabels: { enabled: false },
                tooltip: {
                    theme: 'dark',
                    y: { formatter: function (val) { return val + " leads" } }
                }
            };
            
            if(document.querySelector("#analytics-chart")) {
                var chart = new ApexCharts(document.querySelector("#analytics-chart"), options);
                chart.render();
            }
        });
    </script>
</x-app-layout>
