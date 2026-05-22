<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add fake API usage
        $today = now()->toDateString();
        \App\Models\ApiUsage::insert([
            ['user_id' => 1, 'service' => 'openai', 'tokens_used' => 8400000, 'credits_used' => 0, 'date' => $today, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 1, 'service' => 'midjourney', 'tokens_used' => 0, 'credits_used' => 1240, 'date' => $today, 'created_at' => now(), 'updated_at' => now()]
        ]);

        // Add fake subscriptions
        $subscriptions = [];
        for ($i = 0; $i < 2150; $i++) {
            $subscriptions[] = ['user_id' => 1, 'type' => 'starter', 'stripe_id' => 'sub_fake_'.$i, 'stripe_status' => 'active', 'stripe_price' => 'price_starter', 'quantity' => 1, 'created_at' => now(), 'updated_at' => now()];
        }
        for ($i = 0; $i < 1120; $i++) {
            $subscriptions[] = ['user_id' => 1, 'type' => 'pro', 'stripe_id' => 'sub_fake_pro_'.$i, 'stripe_status' => 'active', 'stripe_price' => 'price_pro', 'quantity' => 1, 'created_at' => now(), 'updated_at' => now()];
        }
        for ($i = 0; $i < 212; $i++) {
            $subscriptions[] = ['user_id' => 1, 'type' => 'agency', 'stripe_id' => 'sub_fake_agency_'.$i, 'stripe_status' => 'active', 'stripe_price' => 'price_agency', 'quantity' => 1, 'created_at' => now(), 'updated_at' => now()];
        }

        // Insert in chunks to avoid memory limit
        foreach (array_chunk($subscriptions, 500) as $chunk) {
            \Illuminate\Support\Facades\DB::table('subscriptions')->insert($chunk);
        }
    }
}
