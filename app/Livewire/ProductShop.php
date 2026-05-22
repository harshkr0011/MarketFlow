<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ProductShop extends Component
{
    public $search = '';
    public $selectedCategory = 'All';
    public $categories = [];

    // Review properties
    public $selectedProductForReview = null;
    public $rating = 5;
    public $comment = '';

    // Checkout properties
    public $selectedProductForCheckout = null;
    public $paymentMethod = 'card'; // 'card' or 'upi'
    public $upiId = '';
    public $cardNumber = '4242 4242 4242 4242';
    public $cardExpiry = '12/28';
    public $cardCvc = '123';
    public $isPurchased = false;
    public $invoiceData = null;

    public function mount()
    {
        $this->categories = Product::distinct()->pluck('category')->toArray();
    }

    public function selectProductForReview($productId)
    {
        $this->selectedProductForReview = Product::find($productId);
        $this->rating = 5;
        $this->comment = '';
    }

    public function submitReview()
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        Review::create([
            'user_id' => Auth::id() ?? (\App\Models\User::first()?->id ?? 1),
            'product_id' => $this->selectedProductForReview->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
        ]);

        $this->dispatch('show-toast', message: 'Review submitted successfully!', type: 'success');
        $this->selectedProductForReview = null;
    }

    public function startCheckout($productId)
    {
        $this->selectedProductForCheckout = Product::find($productId);
        $this->isPurchased = false;
        $this->invoiceData = null;
    }

    public function closeCheckout()
    {
        $this->selectedProductForCheckout = null;
        $this->isPurchased = false;
        $this->invoiceData = null;
    }

    public function closeReview()
    {
        $this->selectedProductForReview = null;
    }

    public function processCheckout()
    {
        if (!$this->selectedProductForCheckout) return;

        if ($this->paymentMethod === 'upi') {
            $this->validate([
                'upiId' => ['required', 'regex:/^[a-zA-Z0-9.\-_]{2,256}@[a-zA-Z]{2,64}$/']
            ], [
                'upiId.required' => 'UPI ID is required for payment.',
                'upiId.regex' => 'Please enter a valid UPI ID (e.g., name@upi or mobile@ybl).'
            ]);
        } else {
            $this->validate([
                'cardNumber' => 'required|min:12',
                'cardExpiry' => 'required|regex:/^\d{2}\/\d{2}$/',
                'cardCvc' => 'required|numeric|digits_between:3,4',
            ], [
                'cardExpiry.regex' => 'Card expiry must be in MM/YY format.'
            ]);
        }

        // Perform mockup purchase success
        $this->isPurchased = true;
        
        // Generate Invoice Mockup Data (Base price in database is INR)
        $subtotal = $this->selectedProductForCheckout->price;
        $tax = round($subtotal * 0.18, 2); // 18% GST simulation
        $total = $subtotal + $tax;

        // Exchange rate conversion: 1 USD = 83 INR
        $usdSubtotal = round($subtotal / 83.0, 2);
        $usdTax = round($tax / 83.0, 2);
        $usdTotal = round($total / 83.0, 2);

        $this->invoiceData = [
            'invoice_no' => 'INV-' . strtoupper(bin2hex(random_bytes(4))),
            'date' => now()->format('Y-m-d H:i'),
            'product_name' => $this->selectedProductForCheckout->name,
            'price' => $subtotal,
            'usd_price' => $usdSubtotal,
            'tax' => $tax, // explicitly mapped as GST
            'usd_tax' => $usdTax,
            'total' => $total,
            'usd_total' => $usdTotal,
            'user_name' => Auth::user() ? Auth::user()->name : 'Guest Customer',
            'user_email' => Auth::user() ? Auth::user()->email : 'guest@marketflow.com',
            'transaction_id' => 'TXN-' . strtoupper(bin2hex(random_bytes(6))),
            'payment_method' => strtoupper($this->paymentMethod),
            'upi_id' => $this->paymentMethod === 'upi' ? $this->upiId : null,
        ];

        // Deduct stock if positive
        if ($this->selectedProductForCheckout->stock > 0) {
            $this->selectedProductForCheckout->decrement('stock');
        }

        $this->dispatch('show-toast', message: 'Payment processed & invoice generated!', type: 'success');
    }

    public function downloadInvoice()
    {
        if (!$this->invoiceData) return null;

        $invoice = $this->invoiceData;
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', compact('invoice'));

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $invoice['invoice_no'] . '.pdf'
        );
    }

    public function render()
    {
        $query = Product::query()->with('reviews.user');

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedCategory !== 'All') {
            $query->where('category', $this->selectedCategory);
        }

        $products = $query->get();

        return view('livewire.product-shop', compact('products'));
    }
}
