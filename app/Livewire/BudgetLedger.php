<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Budget;
use App\Models\BudgetDrawdown;
use App\Models\Campaign;

class BudgetLedger extends Component
{
    public $budgets;
    public $drawdowns;
    public $campaigns;

    // Form fields for request
    public $selected_budget_id;
    public $selected_campaign_id;
    public $amount_requested;

    public function mount()
    {
        $this->loadData();
        $this->campaigns = Campaign::all();
        
        $firstBudget = Budget::first();
        if ($firstBudget) {
            $this->selected_budget_id = $firstBudget->id;
        }

        $firstCampaign = Campaign::first();
        if ($firstCampaign) {
            $this->selected_campaign_id = $firstCampaign->id;
        }
    }

    public function loadData()
    {
        $this->budgets = Budget::with('drawdowns')->orderBy('id', 'desc')->get();
        $this->drawdowns = BudgetDrawdown::with('budget', 'campaign')->orderBy('id', 'desc')->get();
    }

    public function requestDrawdown()
    {
        $this->validate([
            'selected_budget_id' => 'required|exists:budgets,id',
            'selected_campaign_id' => 'required|exists:campaigns,id',
            'amount_requested' => 'required|numeric|min:1',
        ]);

        $budget = Budget::find($this->selected_budget_id);

        // 1. Calculate allocated and current approved drawdowns
        $approvedSum = BudgetDrawdown::where('budget_id', $this->selected_budget_id)
            ->where('status', 'Approved')
            ->sum('amount_requested');

        $pendingSum = BudgetDrawdown::where('budget_id', $this->selected_budget_id)
            ->where('status', 'Pending')
            ->sum('amount_requested');

        $totalProjected = $approvedSum + $pendingSum + $this->amount_requested;

        if ($totalProjected > $budget->total_amount) {
            $this->addError('amount_requested', 'Over budget! The requested drawdown exceeds the total allocated budget limit.');
            return;
        }

        BudgetDrawdown::create([
            'budget_id' => $this->selected_budget_id,
            'campaign_id' => $this->selected_campaign_id,
            'amount_requested' => $this->amount_requested,
            'status' => 'Pending',
        ]);

        $this->amount_requested = null;
        $this->loadData();

        $this->dispatch('show-toast', message: 'Budget drawdown request submitted to Finance Admin successfully!');
    }

    public function render()
    {
        return view('livewire.budget-ledger');
    }
}
