<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Post;
use App\Models\Ticket;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Str;

class CoreAndAdvancedSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $userId = $user ? $user->id : 1;

        // 1. Seed Products
        $products = [
            [
                'name' => 'MarketFlow Premium Suite',
                'slug' => 'marketflow-premium-suite',
                'description' => 'The ultimate AI-driven CRM and marketing automation engine. Enforce compliance, optimize budgets, and run campaigns seamlessly.',
                'category' => 'Software',
                'price' => 14999.00,
                'stock' => 99,
                'image_url' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'name' => 'Growth Plan Consulting',
                'slug' => 'growth-plan-consulting',
                'description' => '1-on-1 strategic growth consulting with veteran marketing experts. Optimize CAC, MER, and scale your brand globally.',
                'category' => 'Consulting',
                'price' => 79999.00,
                'stock' => 5,
                'image_url' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'name' => 'AI Ad Visual Asset Pack',
                'slug' => 'ai-ad-visual-asset-pack',
                'description' => 'A pack of 50 conversion-optimized social media templates for Meta, Google, and LinkedIn ads, custom generated for SaaS and E-commerce.',
                'category' => 'Digital',
                'price' => 2499.00,
                'stock' => 1000,
                'image_url' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'name' => 'Stripe Automation Bridge',
                'slug' => 'stripe-automation-bridge',
                'description' => 'Seamless Laravel Cashier Stripe webhook wrapper to automate invoice generation, billing portal sync, and customer notifications.',
                'category' => 'Software',
                'price' => 4999.00,
                'stock' => 150,
                'image_url' => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'name' => 'SEO Optimization Audit',
                'slug' => 'seo-optimization-audit',
                'description' => 'Comprehensive manual audit of your website SEO structure, core web vitals, backlink profile, and content hierarchy.',
                'category' => 'Services',
                'price' => 11999.00,
                'stock' => 50,
                'image_url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=400&q=80',
            ],
        ];

        foreach ($products as $pData) {
            $product = Product::firstOrCreate(['slug' => $pData['slug']], $pData);

            // Seed 2-3 reviews per product
            Review::firstOrCreate(
                ['product_id' => $product->id, 'user_id' => $userId, 'comment' => 'Absolutely revolutionized how we manage our client campaigns. Fully worth the price!'],
                ['rating' => 5]
            );

            Review::firstOrCreate(
                ['product_id' => $product->id, 'user_id' => $userId, 'comment' => 'Great product, but customer support could be a bit quicker. Satisfied overall.'],
                ['rating' => 4]
            );
        }

        // 2. Seed Posts (CMS Blog)
        $posts = [
            [
                'title' => 'Mastering Multi-Channel Marketing with MarketFlow',
                'slug' => 'mastering-multi-channel-marketing',
                'content' => "In today's digital landscape, relying on a single traffic source is a recipe for stagnation. To scale your brand efficiently, you must allocate budget dynamically across Meta, Google, LinkedIn, and YouTube.

Here is how you can use MarketFlow to maximize your Marketing Efficiency Ratio (MER):
1. Track ad spend live and align it with regional conversions.
2. Maintain strict brand compliance margins using dynamic layouts.
3. Optimize CAC by shifting budget to higher-performing campaigns.

By coordinating your messaging and utilizing AI-generated copies tailored to each channel, you can boost lead velocity and maintain stable growth.",
                'seo_title' => 'Multi-Channel Marketing Strategy | MarketFlow Blog',
                'seo_description' => 'Learn how to optimize CAC and MER across channels using MarketFlow CRM.',
                'image_url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=400&q=80',
                'is_published' => true,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'How AI is Revolutionizing Brand Compliance',
                'slug' => 'ai-revolutionizing-brand-compliance',
                'content' => "Managing a brand image across hundreds of active campaigns can be a logistical nightmare. Traditionally, compliance officers had to review every single visual manual.

With AI and compliance automation:
- Colors can be scanned instantly against HSL palettes.
- Font hierarchy (such as Outfit and Inter) is verified automatically.
- Copy tone is verified for brand guidelines before it goes live.

Using MarketFlow's AI Creative Lab and compliance drawer, you can run automated checks, download compliance reports, and scale campaigns with complete confidence.",
                'seo_title' => 'AI Brand Compliance Automation Guide',
                'seo_description' => 'Discover how AI compliance scanners help preserve brand integrity across networks.',
                'image_url' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=400&q=80',
                'is_published' => true,
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => '10 Ways to Lower Customer Acquisition Cost (CAC)',
                'slug' => 'lower-customer-acquisition-cost',
                'content' => "Customer Acquisition Cost (CAC) is the most critical metric for any scaling business. If your CAC is higher than your customer Lifetime Value (LTV), your model is unsustainable.

Here are 10 actionable tips to decrease your CAC today:
1. Target high-intent search queries.
2. Automate WhatsApp follow-ups for warm leads.
3. Optimize landing page speed and vitals.
4. Run regular A/B testing on ad copies.
5. Implement smart recommendation engines.

Integrating these steps within a single unified dashboard allows your team to react to budget leaks in real time, saving thousands in ad spend.",
                'seo_title' => '10 Tips to Lower CAC | MarketFlow Guide',
                'seo_description' => 'Check out our 10-step guide to lower CAC and improve LTV:CAC ratios.',
                'image_url' => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?auto=format&fit=crop&w=400&q=80',
                'is_published' => true,
                'published_at' => now()->subDay(),
            ],
        ];

        foreach ($posts as $postData) {
            Post::firstOrCreate(['slug' => $postData['slug']], $postData);
        }

        // 3. Seed Support Tickets
        $tickets = [
            [
                'user_id' => $userId,
                'subject' => 'Stripe Webhook Connection Warning',
                'description' => 'We received a warning from Stripe dashboard indicating that the webhook signature verification failed twice. We need to verify if the client secret was rotated.',
                'status' => 'Closed',
                'priority' => 'High',
            ],
            [
                'user_id' => $userId,
                'subject' => 'Midjourney API Limit Reached',
                'description' => 'The Midjourney credits for this month are showing negative balance. We need to upgrade our account subscription to Pro to get more fast generation hours.',
                'status' => 'In Progress',
                'priority' => 'Medium',
            ],
            [
                'user_id' => $userId,
                'subject' => 'Custom Workspace Font Request',
                'description' => 'Can we add support for custom Google fonts in our compliance drawer? We would like to add Outfit and Roboto as defaults.',
                'status' => 'Open',
                'priority' => 'Low',
            ],
        ];

        foreach ($tickets as $tData) {
            Ticket::firstOrCreate([
                'user_id' => $tData['user_id'],
                'subject' => $tData['subject'],
            ], $tData);
        }
    }
}
