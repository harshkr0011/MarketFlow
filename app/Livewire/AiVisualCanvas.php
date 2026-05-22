<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use Illuminate\Support\Str;

class AiVisualCanvas extends Component
{
    public $prompt = '';
    public $style = 'Photorealistic';
    public $generatedImage = '';
    public $isLoading = false;
    
    // Export fields
    public $exportTitle = '';
    public $exportCategory = 'Social Media';
    public $exportPriceTier = 'Free';
    public $isExported = false;

    public function generate()
    {
        $this->validate([
            'prompt' => 'required|min:3'
        ]);

        $this->isLoading = true;
        $this->isExported = false;

        // Clean up prompt and add style modifier
        $fullPrompt = $this->prompt . ' in ' . $this->style . ' style, marketing asset, high resolution, 4k, professional photography';
        $seed = rand(1, 999999);
        
        // Generate via pollinations.ai
        $this->generatedImage = 'https://image.pollinations.ai/prompt/' . urlencode($fullPrompt) . '?width=800&height=800&nologo=true&seed=' . $seed;
        
        $this->exportTitle = 'AI Ad: ' . Str::limit($this->prompt, 20);
        $this->isLoading = false;
    }

    public function export()
    {
        if (!auth()->check()) {
            $this->addError('exportTitle', 'You must be signed in to export assets.');
            return;
        }

        $this->validate([
            'exportTitle' => 'required|min:3',
            'exportCategory' => 'required',
            'exportPriceTier' => 'required'
        ]);

        if (empty($this->generatedImage)) {
            return;
        }

        Asset::create([
            'user_id' => auth()->id(),
            'agency_id' => auth()->user()->agency_id ?? 1,
            'title' => $this->exportTitle,
            'type' => 'Image',
            'file_path' => $this->generatedImage,
            'thumbnail_path' => $this->generatedImage,
            'category' => $this->exportCategory,
            'is_global' => false,
            'price_tier' => $this->exportPriceTier,
        ]);

        $this->isExported = true;
        
        // Dispatch browser event to notify parent toast system
        $this->dispatch('show-toast', message: 'Asset exported to Vault successfully!', type: 'success');
    }

    public function render()
    {
        return view('livewire.ai-visual-canvas');
    }
}
