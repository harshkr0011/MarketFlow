<div class="space-y-6">
    <!-- Header with Search -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-navy-800/40 backdrop-blur-xl border border-navy-700/50 p-6 rounded-2xl">
        <div>
            <h3 class="text-xl font-bold text-white font-outfit">CMS Blog & News</h3>
            <p class="text-sm text-slate-400 font-inter">Explore strategies, compliance tips, and agency insights.</p>
        </div>
        <div class="w-full md:w-80">
            <label class="sr-only">Search articles</label>
            <div class="relative">
                <input wire:model.live="search" type="text" placeholder="Search articles..." 
                    class="w-full bg-navy-900/60 border border-navy-700 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-neon-purple transition-all">
                <span class="absolute left-3.5 top-3.5 text-slate-500">🔍</span>
            </div>
        </div>
    </div>

    <!-- Blog Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($posts as $post)
            <div class="bg-gradient-to-br from-navy-800 to-navy-900 border border-navy-700 rounded-2xl overflow-hidden shadow-2xl hover:border-neon-purple/40 hover:-translate-y-1 transition-all duration-300 flex flex-col group">
                <!-- Thumbnail -->
                <div class="relative h-44 w-full bg-navy-950 overflow-hidden">
                    <img src="{{ $post->image_url ?? 'https://images.unsplash.com/photo-1460925895917-afdab827c52f' }}" alt="{{ $post->title }}" 
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80">
                    <span class="absolute top-4 left-4 bg-navy-900/80 backdrop-blur-md text-[10px] font-bold text-neon-purple border border-neon-purple/20 px-2.5 py-1 rounded-full uppercase">
                        Insights
                    </span>
                </div>

                <!-- Card Content -->
                <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                    <div>
                        <span class="text-[10px] font-mono text-slate-500">
                            Published: {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}
                        </span>
                        <h4 class="text-base font-bold text-white font-outfit mt-2 line-clamp-2 group-hover:text-neon-purple transition-colors">
                            {{ $post->title }}
                        </h4>
                        <p class="text-xs text-slate-400 font-inter mt-2 line-clamp-3 leading-relaxed">
                            {{ strip_tags($post->content) }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-navy-700/50">
                        <button wire:click="selectPost({{ $post->id }})" 
                            class="w-full py-2 bg-navy-800 hover:bg-navy-750 border border-navy-700 hover:border-neon-purple text-slate-300 hover:text-white rounded-xl text-xs font-semibold transition-all flex items-center justify-center gap-1.5">
                            📖 Read Article
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-navy-800/20 border border-navy-700 p-12 rounded-2xl text-center">
                <span class="text-4xl">📰</span>
                <h4 class="text-white font-bold mt-4 font-outfit">No articles found</h4>
                <p class="text-xs text-slate-400 mt-2">Check back later or search with a different keyword.</p>
            </div>
        @endforelse
    </div>

    <!-- Reading Modal -->
    @if($selectedPost)
        <div wire:key="reading-modal-{{ $selectedPost->id }}"
             x-data="{ show: true }"
             x-show="show"
             x-on:click.self="show = false; $wire.closePost()"
             x-on:keydown.window.escape.window="show = false; $wire.closePost()"
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm p-4 flex justify-center items-start sm:items-center">
            <div class="w-full max-w-3xl bg-navy-900 border border-navy-800 rounded-2xl shadow-2xl overflow-hidden flex flex-col my-8">
                <!-- Header -->
                <div class="px-6 py-4 bg-navy-950/50 border-b border-navy-800 flex justify-between items-center">
                    <span class="text-xs text-neon-purple font-mono uppercase font-bold">Reading Article</span>
                    <button type="button" x-on:click="show = false; $wire.closePost()" class="text-slate-400 hover:text-white transition text-lg">✕</button>
                </div>

                <!-- Scrollable Content -->
                <div class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6 max-h-[70vh]">
                    @if($selectedPost->image_url)
                        <img src="{{ $selectedPost->image_url }}" alt="{{ $selectedPost->title }}" class="w-full h-64 object-cover rounded-xl border border-navy-800" />
                    @endif

                    <div class="space-y-2">
                        <div class="flex items-center gap-3 text-xs text-slate-500">
                            <span>Published: {{ $selectedPost->published_at ? $selectedPost->published_at->format('F d, Y') : $selectedPost->created_at->format('F d, Y') }}</span>
                            <span>•</span>
                            <span class="text-neon-purple font-bold">MarketFlow Editorial</span>
                        </div>
                        <h2 class="text-2xl font-black text-white font-outfit leading-tight">{{ $selectedPost->title }}</h2>
                    </div>

                    <!-- SEO Meta tags summary for visibility -->
                    @if($selectedPost->seo_description)
                        <div class="p-4 bg-navy-950/40 border-l-4 border-neon-purple rounded-r-xl text-xs text-slate-400 italic">
                            {{ $selectedPost->seo_description }}
                        </div>
                    @endif

                    <!-- Body -->
                    <div class="text-slate-300 font-inter text-sm leading-relaxed space-y-4 prose-custom">
                        {!! nl2br(e($selectedPost->content)) !!}
                    </div>
                </div>

                <!-- Footer Action -->
                <div class="px-6 py-4 bg-navy-950/50 border-t border-navy-800 flex justify-end">
                    <button type="button" x-on:click="show = false; $wire.closePost()" 
                        class="px-5 py-2.5 bg-navy-800 hover:bg-navy-750 text-slate-300 rounded-xl text-xs font-bold transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
    <style>
        .prose-custom p {
            margin-bottom: 1.25rem;
        }
        .prose-custom ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin-bottom: 1.25rem;
        }
        .prose-custom ol {
            list-style-type: decimal;
            padding-left: 1.5rem;
            margin-bottom: 1.25rem;
        }
        .prose-custom li {
            margin-bottom: 0.5rem;
        }
    </style>
</div>
