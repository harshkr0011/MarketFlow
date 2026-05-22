<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Extend assets table
        Schema::table('assets', function (Blueprint $table) {
            $table->integer('version_major')->default(1);
            $table->integer('version_minor')->default(0);
            $table->foreignId('parent_asset_id')->nullable()->constrained('assets')->nullOnDelete();
            $table->text('customized_fields_json')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('territory_restriction')->nullable();
        });

        // 2. Create partners table
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('email')->unique();
            $table->string('status')->default('Onboarded'); // Onboarded, Suspended
            $table->timestamps();
        });

        // 3. Create rfps table
        Schema::create('rfps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('budget_limit', 15, 2);
            $table->date('deadline');
            $table->timestamps();
        });

        // 4. Create proposals table
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rfp_id')->constrained()->cascadeOnDelete();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->decimal('bid_amount', 15, 2);
            $table->string('proposal_file_path')->nullable();
            $table->string('status')->default('Submitted'); // Submitted, Awarded, Declined
            $table->timestamps();
        });

        // 5. Create ad_spend_logs table
        Schema::create('ad_spend_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('platform'); // Meta, Google, TikTok, Email
            $table->decimal('spend_amount', 15, 2);
            $table->string('currency')->default('INR');
            $table->date('recorded_date');
            $table->timestamps();
        });

        // 6. Create budgets table
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('scope'); // Department, Franchise, Campaign
            $table->unsignedBigInteger('owner_id'); // User ID or Agency ID
            $table->decimal('total_amount', 15, 2);
            $table->string('currency')->default('INR');
            $table->string('fiscal_year');
            $table->timestamps();
        });

        // 7. Create budget_drawdowns table
        Schema::create('budget_drawdowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount_requested', 15, 2);
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_drawdowns');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('ad_spend_logs');
        Schema::dropIfExists('proposals');
        Schema::dropIfExists('rfps');
        Schema::dropIfExists('partners');
        
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['parent_asset_id']);
            $table->dropColumn([
                'version_major',
                'version_minor',
                'parent_asset_id',
                'customized_fields_json',
                'expires_at',
                'territory_restriction',
            ]);
        });
    }
};
