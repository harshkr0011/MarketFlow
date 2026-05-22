<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Partner;
use App\Models\Rfp;
use App\Models\Proposal;
use App\Models\Budget;
use App\Models\BudgetDrawdown;
use App\Models\Campaign;

class EPortalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Partners
        $partner1 = Partner::firstOrCreate(
            ['email' => 'partner@pixelperfect.com'],
            ['company_name' => 'PixelPerfect Agency', 'status' => 'Onboarded']
        );

        $partner2 = Partner::firstOrCreate(
            ['email' => 'print@quickprint.com'],
            ['company_name' => 'QuickPrint Solutions', 'status' => 'Onboarded']
        );

        // Get or create a campaign
        $campaign = Campaign::first() ?? Campaign::create([
            'user_id' => \App\Models\User::first()->id ?? 1,
            'workspace_id' => \App\Models\Workspace::first()->id ?? 1,
            'title' => 'Summer Launch Campaign',
            'status' => 'Design',
        ]);

        // 2. Seed RFPs
        $rfp1 = Rfp::firstOrCreate(
            ['title' => 'Q2 Social Campaign Video Production'],
            [
                'campaign_id' => $campaign->id,
                'description' => 'Create a 30-second premium video ad showcasing our eco-friendly packaging for Instagram & YouTube ads.',
                'budget_limit' => 250000.00,
                'deadline' => now()->addDays(14)->toDateString(),
            ]
        );

        $rfp2 = Rfp::firstOrCreate(
            ['title' => 'Offline Store Pop-up Banner Printing'],
            [
                'campaign_id' => $campaign->id,
                'description' => 'Print 5 retractable roll-up banners and 10 promotional flyers on recycled cardstock.',
                'budget_limit' => 75000.00,
                'deadline' => now()->addDays(5)->toDateString(),
            ]
        );

        // 3. Seed Proposals
        Proposal::firstOrCreate(
            ['rfp_id' => $rfp1->id, 'partner_id' => $partner1->id],
            [
                'bid_amount' => 220000.00,
                'proposal_file_path' => 'proposals/pixelperfect_bid.pdf',
                'status' => 'Submitted',
            ]
        );

        Proposal::firstOrCreate(
            ['rfp_id' => $rfp2->id, 'partner_id' => $partner2->id],
            [
                'bid_amount' => 68000.00,
                'proposal_file_path' => 'proposals/quickprint_bid.pdf',
                'status' => 'Awarded',
            ]
        );

        // 4. Seed Budget
        $budget = Budget::firstOrCreate(
            ['scope' => 'Franchise/General', 'fiscal_year' => 'FY 2026'],
            [
                'owner_id' => 1,
                'total_amount' => 1500000.00,
                'currency' => 'INR',
            ]
        );

        // 5. Seed Budget Drawdowns
        BudgetDrawdown::firstOrCreate(
            ['budget_id' => $budget->id, 'campaign_id' => $campaign->id, 'amount_requested' => 327860.00],
            ['status' => 'Approved']
        );

        BudgetDrawdown::firstOrCreate(
            ['budget_id' => $budget->id, 'campaign_id' => $campaign->id, 'amount_requested' => 150000.00],
            ['status' => 'Pending']
        );
    }
}
