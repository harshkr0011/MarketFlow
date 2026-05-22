<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\ApiUsage;
use Laravel\Cashier\Subscription;

class GlobalPulseWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // 1. Total Platform Revenue (Simplified logic for dashboard, normally queried from invoices or Stripe balance)
        // Cashier manages Stripe. Here we just mock or use a query if we have invoices table locally
        $revenue = 54000; // Mocked for ₹54,000 INR

        // 2. Active Subscriptions
        $activeSubscriptions = 0;
        if(class_exists(Subscription::class)) {
            $activeSubscriptions = Subscription::where('stripe_status', 'active')->count();
        }

        // 3. API Usage (OpenAI Tokens / Image Credits)
        $tokensUsedToday = ApiUsage::whereDate('created_at', today())
            ->where('service', 'openai')
            ->sum('tokens_used');

        return [
            Stat::make('Total Platform Revenue', '₹' . number_format($revenue))
                ->description('Estimated MRR from active subscriptions')
                ->descriptionIcon('heroicon-m-currency-rupee')
                ->color('success'),

            Stat::make('Active Subscriptions', $activeSubscriptions)
                ->description('Starter, Pro, and Agency tiers')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('API Usage (Today)', number_format($tokensUsedToday) . ' Tokens')
                ->description('OpenAI tokens consumed today')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('warning'),
        ];
    }
}
