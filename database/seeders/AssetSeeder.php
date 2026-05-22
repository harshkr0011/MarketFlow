<?php

namespace Database\Seeders;

use App\Models\Asset;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $assets = [
            // Social Media Category (Figma)
            [
                'user_id' => 1,
                'agency_id' => 1,
                'title' => 'SaaS Hero Banner Template',
                'type' => 'Figma',
                'thumbnail_path' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=300&q=80',
                'file_path' => '/downloads/saas-hero-banner.fig',
                'category' => 'Social Media',
                'is_global' => true,
                'price_tier' => 'Free',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'agency_id' => 1,
                'title' => 'Instagram Carousel Post Pack',
                'type' => 'Figma',
                'thumbnail_path' => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=300&q=80',
                'file_path' => '/downloads/instagram-pack.fig',
                'category' => 'Social Media',
                'is_global' => true,
                'price_tier' => 'Pro',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'agency_id' => 1,
                'title' => 'LinkedIn Brand Banner templates',
                'type' => 'Figma',
                'thumbnail_path' => 'https://images.unsplash.com/photo-1618005198143-e528346d9a59?auto=format&fit=crop&w=300&q=80',
                'file_path' => '/downloads/linkedin-banners.fig',
                'category' => 'Social Media',
                'is_global' => true,
                'price_tier' => 'Free',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Email Category (HTML)
            [
                'user_id' => 1,
                'agency_id' => 1,
                'title' => 'Cart Abandonment Email Sequence',
                'type' => 'HTML',
                'thumbnail_path' => 'https://images.unsplash.com/photo-1557200134-90327ee9fafa?auto=format&fit=crop&w=300&q=80',
                'file_path' => '/downloads/cart-abandonment.zip',
                'category' => 'Email',
                'is_global' => true,
                'price_tier' => 'Pro',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'agency_id' => 1,
                'title' => 'SaaS Product Update Newsletter',
                'type' => 'HTML',
                'thumbnail_path' => 'https://images.unsplash.com/photo-1579546929518-9e396f3cc809?auto=format&fit=crop&w=300&q=80',
                'file_path' => '/downloads/product-newsletter.html',
                'category' => 'Email',
                'is_global' => true,
                'price_tier' => 'Free',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Playbook Category (PDF)
            [
                'user_id' => 1,
                'agency_id' => 1,
                'title' => 'High-Conversion Funnel Blueprint',
                'type' => 'PDF',
                'thumbnail_path' => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?auto=format&fit=crop&w=300&q=80',
                'file_path' => '/downloads/funnel-blueprint.pdf',
                'category' => 'Playbook',
                'is_global' => true,
                'price_tier' => 'Pro',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'agency_id' => 1,
                'title' => 'SaaS Scaling Handbook 2026',
                'type' => 'PDF',
                'thumbnail_path' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=300&q=80',
                'file_path' => '/downloads/saas-scaling.pdf',
                'category' => 'Playbook',
                'is_global' => true,
                'price_tier' => 'Free',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Asset::insert($assets);
    }
}
