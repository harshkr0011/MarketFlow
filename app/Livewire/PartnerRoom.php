<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Rfp;
use App\Models\Proposal;
use App\Models\Campaign;

class PartnerRoom extends Component
{
    public $rfps;
    public $proposals;

    // Form fields for creating a new RFP
    public $title;
    public $description;
    public $budget_limit;
    public $deadline;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->rfps = Rfp::with('campaign', 'proposals.partner')->orderBy('id', 'desc')->get();
        $this->proposals = Proposal::with('rfp', 'partner')->orderBy('id', 'desc')->get();
    }

    public function createRfp()
    {
        $this->validate([
            'title' => 'required|string|min:3',
            'description' => 'required|string',
            'budget_limit' => 'required|numeric|min:1',
            'deadline' => 'required|date|after_or_equal:today',
        ]);

        $campaign = Campaign::first();

        Rfp::create([
            'campaign_id' => $campaign ? $campaign->id : null,
            'title' => $this->title,
            'description' => $this->description,
            'budget_limit' => $this->budget_limit,
            'deadline' => $this->deadline,
        ]);

        $this->reset(['title', 'description', 'budget_limit', 'deadline']);
        $this->loadData();

        $this->dispatch('show-toast', message: 'RFP Brief dispatched to partner network successfully!');
    }

    public function render()
    {
        return view('livewire.partner-room');
    }
}
