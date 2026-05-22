<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Post;
use App\Models\Ticket;
use App\Models\Review;
use App\Models\User;

class MarketFlowSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            return;
        }

        // 1. Seed Products
        $p1 = Product::firstOrCreate(
            ['slug' => 'premium-social-media-bundle'],
            [
                'name' => 'Premium Social Media Bundle',
                'description' => 'A collection of 50+ fully customizable Figma and Photoshop templates optimized for conversion across Meta, TikTok, and LinkedIn.',
                'category' => 'Templates',
                'price' => 49.99,
                'stock' => 950,
                'image_url' => 'https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7',
            ]
        );

        $p2 = Product::firstOrCreate(
            ['slug' => 'email-nurture-sequence-copy'],
            [
                'name' => 'Email Nurture Sequence Copy',
                'description' => 'High-converting 7-day drip campaign copy designed by copywriters to build trust and pitch agency services effectively.',
                'category' => 'Copywriting',
                'price' => 79.99,
                'stock' => 480,
                'image_url' => 'https://images.unsplash.com/photo-1557200134-90327ee9fafa',
            ]
        );

        $p3 = Product::firstOrCreate(
            ['slug' => 'full-funnel-strategy-session'],
            [
                'name' => 'Full Funnel Strategy Session',
                'description' => 'A private 60-minute strategy call with our principal marketing architect to optimize your agency client acquisition funnel.',
                'category' => 'Consulting',
                'price' => 499.00,
                'stock' => 12,
                'image_url' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c',
            ]
        );

        $p4 = Product::firstOrCreate(
            ['slug' => 'seo-keyword-analysis-report'],
            [
                'name' => 'SEO Keyword Analysis Report',
                'description' => 'Comprehensive research report matching high-intent keywords for your agency niche, including competitor difficulty index.',
                'category' => 'SEO',
                'price' => 149.00,
                'stock' => 85,
                'image_url' => 'https://images.unsplash.com/photo-1572021335469-31706a17aaef',
            ]
        );

        // 2. Seed Reviews
        Review::firstOrCreate(
            ['user_id' => $user->id, 'product_id' => $p1->id],
            [
                'rating' => 5,
                'comment' => 'Incredible templates! Saved our team over 20 hours of design work. The layouts are very clean and modern.',
            ]
        );

        Review::firstOrCreate(
            ['user_id' => $user->id, 'product_id' => $p2->id],
            [
                'rating' => 4,
                'comment' => 'The copy structure is great and the logic flows really well. A few minor tweaks needed for our specific tone, but it got us a 3.4% click-through rate.',
            ]
        );

        // 3. Seed CMS Posts
        Post::firstOrCreate(
            ['slug' => '10-tips-to-explode-your-lead-conversion-rate'],
            [
                'title' => '10 Tips to Explode Your Lead Conversion Rate',
                'content' => 'Building a conversion funnel requires focusing on intent, speed, and clear messaging. In this article, we look at lead scoring, instant WhatsApp responders, clear space margin grids, and continuous testing to bump your conversions by up to 45%.',
                'seo_title' => '10 Tips for Higher Lead Conversions | MarketFlow Blog',
                'seo_description' => 'Learn how to optimize conversion rates using lead scoring, automation tools, and strategic layouts.',
                'image_url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f',
                'is_published' => true,
                'published_at' => now(),
            ]
        );

        Post::firstOrCreate(
            ['slug' => 'why-personalization-is-the-future-of-marketing'],
            [
                'title' => 'Why Personalization is the Future of Marketing Automation',
                'content' => 'Generic campaigns no longer convert. Audiences expect customized touchpoints. Discover how dynamic event names, custom user-defined schemas, and behavioral triggers can double your click-through rates and build deep, lasting customer relationships.',
                'seo_title' => 'The Power of Personalized Marketing Automation | MarketFlow',
                'seo_description' => 'Why generic campaigns fail and how custom parameters, dynamic data injection, and segmentation increase customer engagement.',
                'image_url' => 'https://images.unsplash.com/photo-1432888622747-4eb9a8f2c1d8',
                'is_published' => true,
                'published_at' => now()->subDay(),
            ]
        );

        Post::firstOrCreate(
            ['slug' => 'building-intelligent-agency-workflows-with-gemini'],
            [
                'title' => 'Building Intelligent Agency Workflows with Gemini AI',
                'content' => 'Artificial intelligence is changing from a gimmick to a core productivity engine. Learn how we integrated Gemini API directly into content creation pipelines, visual styling builders, and automated user feedback response loops.',
                'seo_title' => 'Agency Workflows and Gemini AI Integration | MarketFlow',
                'seo_description' => 'Explore how artificial intelligence accelerates copy drafting, image generation, and workflow automation in marketing.',
                'image_url' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe',
                'is_published' => true,
                'published_at' => now()->subDays(2),
            ]
        );

        // 4. Seed CRM Tickets
        Ticket::firstOrCreate(
            ['user_id' => $user->id, 'subject' => 'Stripe Mock payment sandbox failure'],
            [
                'description' => 'When running a mock checkout flow for a strategy session, the invoice generated showed incorrect item quantities. Please verify the billing math.',
                'status' => 'Open',
                'priority' => 'High',
            ]
        );

        Ticket::firstOrCreate(
            ['user_id' => $user->id, 'subject' => 'Asset version major tag is locked'],
            [
                'description' => 'I cannot increment the major version number in my asset vault for the summer launch deck. When I click upload, it default saves as v1.1.0 instead of v2.0.0.',
                'status' => 'Closed',
                'priority' => 'Medium',
            ]
        );
    }
}
