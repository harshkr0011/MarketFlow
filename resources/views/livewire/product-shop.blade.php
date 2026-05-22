<div class="space-y-6">
    <!-- Header with Search & Filter -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-navy-800/40 backdrop-blur-xl border border-navy-700/50 p-6 rounded-2xl">
        <div class="flex-1 max-w-md">
            <label class="sr-only">Search products</label>
            <div class="relative">
                <input wire:model.live="search" type="text" placeholder="Search products or services..." 
                    class="w-full bg-navy-900/60 border border-navy-700 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-neon-cyan transition-all">
                <span class="absolute left-3.5 top-3.5 text-slate-500">🔍</span>
            </div>
        </div>
        
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="$set('selectedCategory', 'All')" 
                class="px-4 py-2 rounded-xl text-xs font-semibold border transition-all duration-200 {{ $selectedCategory === 'All' ? 'bg-neon-cyan/20 border-neon-cyan text-white' : 'bg-navy-900/60 border-navy-700 text-slate-400 hover:text-white' }}">
                All Categories
            </button>
            @foreach($categories as $category)
                <button wire:click="$set('selectedCategory', '{{ $category }}')" 
                    class="px-4 py-2 rounded-xl text-xs font-semibold border transition-all duration-200 {{ $selectedCategory === $category ? 'bg-neon-cyan/20 border-neon-cyan text-white' : 'bg-navy-900/60 border-navy-700 text-slate-400 hover:text-white' }}">
                    {{ $category }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($products as $product)
            <div class="bg-gradient-to-br from-navy-800 to-navy-900 border border-navy-700 rounded-2xl overflow-hidden shadow-2xl hover:border-neon-cyan/30 transition-all duration-300 flex flex-col group">
                <!-- Product Media -->
                <div class="relative h-48 w-full bg-navy-950 overflow-hidden">
                    <img src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1542291026-7eec264c27ff' }}" alt="{{ $product->name }}" 
                        class="w-full h-full object-cover group-hover:scale-105 transition-all duration-500">
                    <span class="absolute top-4 left-4 bg-navy-900/80 backdrop-blur-md text-[10px] font-bold text-neon-cyan border border-neon-cyan/20 px-2.5 py-1 rounded-full uppercase tracking-wider">
                        {{ $product->category }}
                    </span>
                    <span class="absolute top-4 right-4 bg-navy-900/80 backdrop-blur-md text-[10px] font-bold text-slate-300 border border-navy-700 px-2.5 py-1 rounded-full">
                        {{ $product->stock }} in stock
                    </span>
                </div>

                <!-- Content -->
                <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <h4 class="text-base font-bold text-white font-outfit line-clamp-1">{{ $product->name }}</h4>
                            <span class="text-base font-black text-neon-cyan font-mono">₹{{ number_format($product->price) }} (${{ number_format(round($product->price / 83)) }})</span>
                        </div>
                        <p class="text-xs text-slate-400 font-inter mt-2 line-clamp-2 leading-relaxed">{{ $product->description }}</p>
                        
                        <!-- Ratings -->
                        <div class="flex items-center gap-2 mt-4">
                            <div class="flex text-amber-400 text-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    <span>{{ $i <= $product->average_rating ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                            <span class="text-[10px] font-semibold text-slate-500">
                                {{ $product->average_rating }} ({{ $product->reviews->count() }} reviews)
                            </span>
                        </div>
                    </div>

                    <!-- Footer actions -->
                    <div class="grid grid-cols-2 gap-3 pt-4 border-t border-navy-700/50">
                        <button wire:click="selectProductForReview({{ $product->id }})" 
                            class="w-full py-2 border border-navy-700 hover:border-slate-500 text-slate-300 hover:text-white rounded-xl text-xs font-semibold transition-all">
                            ✍️ Review
                        </button>
                        <button wire:click="startCheckout({{ $product->id }})" 
                            class="w-full py-2 bg-gradient-to-r from-neon-cyan to-blue-600 hover:from-neon-cyan hover:to-blue-500 text-white rounded-xl text-xs font-bold transition-all shadow-lg shadow-neon-cyan/15">
                            💳 Quick Buy / Checkout
                        </button>
                    </div>
                </div>

                <!-- Review Feed Preview -->
                @if($product->reviews->isNotEmpty())
                    <div class="px-6 pb-6 pt-2 bg-navy-950/20 border-t border-navy-800/30 text-[10px] text-slate-400 space-y-2">
                        <span class="font-bold text-slate-500 uppercase tracking-wider">Latest Review</span>
                        <div class="bg-navy-950/40 p-2.5 rounded-lg border border-navy-800/50">
                            <div class="flex justify-between font-semibold text-slate-300">
                                <span>{{ $product->reviews->last()->user?->name ?? 'Guest User' }}</span>
                                <span class="text-amber-400 font-bold">★ {{ $product->reviews->last()->rating }}</span>
                            </div>
                            <p class="italic mt-1">"{{ $product->reviews->last()->comment }}"</p>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full bg-navy-800/20 border border-navy-700 p-12 rounded-2xl text-center">
                <span class="text-4xl">🛍️</span>
                <h4 class="text-white font-bold mt-4 font-outfit">No products found</h4>
                <p class="text-xs text-slate-400 mt-2">Try adjusting your filters or search query.</p>
            </div>
        @endforelse
    </div>

    <!-- Review Modal -->
    @if($selectedProductForReview)
        <div id="review-modal-backdrop"
             wire:key="review-modal-{{ $selectedProductForReview->id }}"
             x-data="{ show: true }"
             x-show="show"
             x-on:click.self="show = false; $wire.closeReview()"
             x-on:keydown.window.escape.window="show = false; $wire.closeReview()"
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm p-4 flex justify-center items-start sm:items-center">
            <div class="w-full max-w-md bg-navy-900 border border-navy-800 rounded-2xl p-6 shadow-2xl space-y-4 my-8">
                <div class="flex justify-between items-center pb-2 border-b border-navy-800">
                    <h3 class="text-base font-bold text-white font-outfit">Write a Review</h3>
                    <button type="button" x-on:click="show = false; $wire.closeReview()" class="text-slate-400 hover:text-white transition">✕</button>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-neon-cyan">{{ $selectedProductForReview->name }}</h4>
                    <p class="text-[10px] text-slate-400 mt-1">Share your experience with this service or template.</p>
                </div>

                <form wire:submit.prevent="submitReview" class="space-y-4">
                    <!-- Rating Select -->
                    <div class="space-y-1.5">
                        <label class="block text-xs text-slate-400 font-medium">Rating</label>
                        <select wire:model="rating" class="w-full bg-navy-950 border border-navy-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-neon-cyan">
                            <option value="5">⭐⭐⭐⭐⭐ (5 - Excellent)</option>
                            <option value="4">⭐⭐⭐⭐ (4 - Very Good)</option>
                            <option value="3">⭐⭐⭐ (3 - Average)</option>
                            <option value="2">⭐⭐ (2 - Poor)</option>
                            <option value="1">⭐ (1 - Terrible)</option>
                        </select>
                    </div>

                    <!-- Comment -->
                    <div class="space-y-1.5">
                        <label class="block text-xs text-slate-400 font-medium">Review Comment</label>
                        <textarea wire:model="comment" rows="3" placeholder="Tell us what you think..." 
                            class="w-full bg-navy-950 border border-navy-700 rounded-xl px-3 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-neon-cyan"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" x-on:click="show = false; $wire.closeReview()" 
                            class="px-4 py-2 bg-navy-800 hover:bg-navy-750 text-slate-300 rounded-xl text-xs font-semibold transition">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 bg-gradient-to-r from-neon-cyan to-blue-600 text-white rounded-xl text-xs font-bold transition shadow-lg">
                            Submit Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Checkout & Invoice Modal -->
    @if($selectedProductForCheckout)
        <div id="checkout-modal-backdrop"
             wire:key="checkout-modal-{{ $selectedProductForCheckout->id }}"
             x-data="{ show: true }"
             x-show="show"
             x-on:click.self="show = false; $wire.closeCheckout()"
             x-on:keydown.window.escape.window="show = false; $wire.closeCheckout()"
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm p-4 flex justify-center items-start sm:items-center">
            <div class="w-full max-w-xl bg-navy-900 border border-navy-800 rounded-2xl p-6 shadow-2xl space-y-6 my-8">
                <div class="flex justify-between items-center pb-2 border-b border-navy-800">
                    <h3 class="text-base font-bold text-white font-outfit flex items-center gap-2">
                        <span>🔒</span> Razorpay / UPI Secure Checkout (India)
                    </h3>
                    <button type="button" x-on:click="show = false; $wire.closeCheckout()" class="text-slate-400 hover:text-white transition">✕</button>
                </div>

                @if(!$isPurchased)
                    <!-- Checkout details -->
                    @php
                        $subtotal = $selectedProductForCheckout->price;
                        $gst = round($subtotal * 0.18, 2);
                        $total = $subtotal + $gst;
                        $usdSubtotal = round($subtotal / 83.0, 2);
                        $usdGst = round($gst / 83.0, 2);
                        $usdTotal = round($total / 83.0, 2);
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        <!-- Left Panel: Summary -->
                        <div class="md:col-span-5 bg-navy-950/60 p-4 border border-navy-800 rounded-xl space-y-4">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Order Summary</h4>
                            <div class="space-y-3">
                                <img src="{{ $selectedProductForCheckout->image_url }}" class="w-full h-24 object-cover rounded-lg border border-navy-800" />
                                <div>
                                    <h5 class="text-xs font-bold text-white leading-tight">{{ $selectedProductForCheckout->name }}</h5>
                                    <span class="text-[10px] text-neon-cyan font-bold uppercase block mt-1">{{ $selectedProductForCheckout->category }}</span>
                                </div>
                                <div class="border-t border-navy-800 pt-3 space-y-2 text-xs font-bold">
                                    <div class="flex justify-between text-slate-400">
                                        <span>Subtotal:</span>
                                        <span class="font-mono text-slate-300">₹{{ number_format($subtotal, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-slate-400">
                                        <span>GST (18%):</span>
                                        <span class="font-mono text-slate-300">₹{{ number_format($gst, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-white border-t border-navy-800 pt-2 text-xs">
                                        <span>Grand Total:</span>
                                        <span class="text-neon-cyan font-mono">₹{{ number_format($total, 2) }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 text-right font-medium italic mt-1">
                                        Approx. ${{ number_format($usdTotal, 2) }} USD
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Panel: Form -->
                        <form wire:submit.prevent="processCheckout" class="md:col-span-7 space-y-4">
                            <!-- Payment Method Selector -->
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Choose Payment Option</label>
                                <div class="grid grid-cols-2 gap-2 bg-navy-950/80 p-1 rounded-xl border border-navy-800">
                                    <button type="button" wire:click="$set('paymentMethod', 'card')" 
                                        class="py-2 text-xs font-semibold rounded-lg transition-all {{ $paymentMethod === 'card' ? 'bg-gradient-to-r from-neon-cyan to-blue-600 text-white shadow' : 'text-slate-400 hover:text-white' }}">
                                        💳 Card
                                    </button>
                                    <button type="button" wire:click="$set('paymentMethod', 'upi')" 
                                        class="py-2 text-xs font-semibold rounded-lg transition-all {{ $paymentMethod === 'upi' ? 'bg-gradient-to-r from-purple-600 to-neon-purple text-white shadow' : 'text-slate-400 hover:text-white' }}">
                                        📱 UPI (Instant)
                                    </button>
                                </div>
                            </div>

                            @if($paymentMethod === 'card')
                                <div class="space-y-4">
                                    <div class="space-y-1.5">
                                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Card Number</label>
                                        <input type="text" wire:model="cardNumber" placeholder="4242 4242 4242 4242"
                                            class="w-full bg-navy-950 border border-navy-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-neon-cyan font-mono">
                                        @error('cardNumber') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-1.5">
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Expires</label>
                                            <input type="text" wire:model="cardExpiry" placeholder="MM/YY" 
                                                class="w-full bg-navy-950 border border-navy-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-neon-cyan font-mono">
                                            @error('cardExpiry') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="space-y-1.5">
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">CVC</label>
                                            <input type="password" wire:model="cardCvc" placeholder="123"
                                                class="w-full bg-navy-950 border border-navy-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-neon-cyan font-mono">
                                            @error('cardCvc') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="space-y-4">
                                    <div class="space-y-1.5">
                                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">UPI ID (VPA)</label>
                                        <input type="text" wire:model="upiId" placeholder="E.g., name@upi or mobile@ybl" 
                                            class="w-full bg-navy-950 border border-navy-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-neon-purple font-mono">
                                        @error('upiId') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- QR Code Scanner Mockup -->
                                    <div class="bg-navy-950/60 p-4 border border-navy-800 rounded-xl flex flex-col items-center justify-center space-y-3">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Or Scan QR Code to Pay</span>
                                        <div class="bg-white p-3 rounded-2xl relative shadow-md" style="width: 144px; height: 144px; display: flex; align-items: center; justify-content: center; box-sizing: border-box;">
                                            <svg class="text-slate-900" style="width: 120px; height: 120px;" viewBox="0 0 100 100" fill="currentColor">
                                                <!-- Corners -->
                                                <rect x="0" y="0" width="25" height="25" />
                                                <rect x="5" y="5" width="15" height="15" fill="white" />
                                                <rect x="8" y="8" width="9" height="9" />
                                                
                                                <rect x="75" y="0" width="25" height="25" />
                                                <rect x="80" y="5" width="15" height="15" fill="white" />
                                                <rect x="83" y="8" width="9" height="9" />

                                                <rect x="0" y="75" width="25" height="25" />
                                                <rect x="5" y="80" width="15" height="15" fill="white" />
                                                <rect x="8" y="83" width="9" height="9" />

                                                <!-- Random mock QR noise blocks -->
                                                <rect x="35" y="5" width="8" height="8" />
                                                <rect x="47" y="10" width="12" height="6" />
                                                <rect x="62" y="3" width="7" height="15" />
                                                <rect x="32" y="20" width="15" height="8" />
                                                
                                                <rect x="30" y="35" width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" />
                                                <rect x="38" y="42" width="24" height="24" />
                                                <rect x="44" y="48" width="12" height="12" fill="white" />
                                                <text x="50" y="56" font-size="8" font-weight="black" fill="currentColor" text-anchor="middle" font-family="sans-serif">UPI</text>

                                                <rect x="5" y="35" width="10" height="18" />
                                                <rect x="18" y="42" width="8" height="12" />
                                                <rect x="12" y="60" width="14" height="6" />

                                                <rect x="78" y="35" width="15" height="8" />
                                                <rect x="85" y="50" width="12" height="18" />
                                                <rect x="74" y="72" width="10" height="10" />

                                                <rect x="35" y="85" width="18" height="10" />
                                                <rect x="60" y="80" width="8" height="14" />
                                            </svg>
                                        </div>
                                        <div class="flex items-center gap-2 text-[10px] text-slate-400 font-semibold mt-1">
                                            <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 rounded-full border border-emerald-500/10">Bhim UPI</span>
                                            <span class="px-2 py-0.5 bg-indigo-500/10 text-indigo-400 rounded-full border border-indigo-500/10">Razorpay Secured</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="pt-4">
                                <button type="submit" 
                                    class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-emerald-500/10 uppercase tracking-wider">
                                    Pay Now (₹{{ number_format($total, 2) }})
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <!-- Invoice layout -->
                    <div class="space-y-6">
                        <!-- Payment Success -->
                        <div class="text-center space-y-2">
                            <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-2xl">✔</span>
                            <h4 class="text-base font-bold text-white font-outfit">Payment Completed Successfully!</h4>
                            <p class="text-xs text-slate-400">An invoice has been generated for your record below.</p>
                        </div>

                        <!-- Generated Invoice -->
                        @if($invoiceData)
                            <div class="bg-navy-950 border border-navy-800 rounded-2xl p-6 font-mono text-xs text-slate-300 space-y-6 shadow-inner relative overflow-hidden">
                                <!-- Watermark background logo -->
                                <div class="absolute -right-6 -bottom-6 text-navy-900 text-7xl font-bold opacity-30 pointer-events-none select-none">
                                    PAID
                                </div>

                                <!-- Invoice Header -->
                                <div class="flex justify-between border-b border-navy-800 pb-4">
                                    <div>
                                        <h3 class="text-white font-black font-outfit text-sm tracking-wide">MARKETFLOW INC.</h3>
                                        <span class="text-[10px] text-slate-500">100 Innovation Way, Tech District</span>
                                    </div>
                                    <div class="text-right">
                                        <h4 class="text-white font-bold">INVOICE</h4>
                                        <span class="text-[10px] text-neon-cyan">{{ $invoiceData['invoice_no'] }}</span>
                                    </div>
                                </div>

                                <!-- Billing Info -->
                                <div class="grid grid-cols-2 gap-4 text-[10px]">
                                    <div>
                                        <span class="block text-slate-500 uppercase font-bold">Billed To:</span>
                                        <strong class="text-slate-300">{{ $invoiceData['user_name'] }}</strong>
                                        <span class="block text-slate-400">{{ $invoiceData['user_email'] }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="block text-slate-500 uppercase font-bold">Transaction Meta:</span>
                                        <span class="block text-slate-400">Date: {{ $invoiceData['date'] }}</span>
                                        <span class="block text-slate-400">Method: {{ $invoiceData['payment_method'] }}</span>
                                        @if($invoiceData['upi_id'])
                                            <span class="block text-slate-400">UPI ID: {{ $invoiceData['upi_id'] }}</span>
                                        @endif
                                        <span class="block text-slate-400">TXN: {{ $invoiceData['transaction_id'] }}</span>
                                    </div>
                                </div>

                                <!-- Line Items -->
                                <table class="w-full text-left text-[11px] border-b border-navy-800 pb-4">
                                    <thead>
                                        <tr class="text-slate-500 border-b border-navy-800 pb-2">
                                            <th class="py-1">Description</th>
                                            <th class="py-1 text-right">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-slate-200">
                                            <td class="py-2">{{ $invoiceData['product_name'] }}</td>
                                            <td class="py-2 text-right">₹{{ number_format($invoiceData['price'], 2) }} (${{ number_format($invoiceData['usd_price'], 2) }})</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- Totals -->
                                <div class="space-y-1.5 text-right text-[10px] border-t border-navy-800 pt-4">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Subtotal:</span>
                                        <span class="text-slate-300">₹{{ number_format($invoiceData['price'], 2) }} (${{ number_format($invoiceData['usd_price'], 2) }})</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">GST (18%):</span>
                                        <span class="text-slate-300">₹{{ number_format($invoiceData['tax'], 2) }} (${{ number_format($invoiceData['usd_tax'], 2) }})</span>
                                    </div>
                                    <div class="flex justify-between text-xs font-bold text-white pt-2 border-t border-navy-800">
                                        <span>Total Paid:</span>
                                        <span class="text-neon-cyan font-mono">₹{{ number_format($invoiceData['total'], 2) }} (${{ number_format($invoiceData['usd_total'], 2) }})</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" wire:click="downloadInvoice" 
                                class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white rounded-xl text-xs font-bold transition shadow-lg flex items-center gap-1.5">
                                📥 Download PDF Invoice
                            </button>
                            <button type="button" x-on:click="show = false; $wire.closeCheckout()" 
                                class="px-5 py-2.5 bg-navy-800 hover:bg-navy-700 text-slate-300 rounded-xl text-xs font-bold transition">
                                Done
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
